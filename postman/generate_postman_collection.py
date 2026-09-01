import json
from pathlib import Path

base = Path(r'c:\laragon\www\microservicios-laravel\postman')
base.mkdir(exist_ok=True)


def make_request(method, raw_url, port, body=None, tests=None):
    request = {
        'method': method,
        'header': [],
        'url': {
            'raw': raw_url,
            'protocol': 'http',
            'host': ['127.0.0.1'],
            'port': str(port),
            'path': raw_url.split('://', 1)[-1].split('/', 1)[-1].split('/') if '/' in raw_url.split('://', 1)[-1] else []
        }
    }

    if body is not None:
        request['header'].append({'key': 'Content-Type', 'value': 'application/json'})
        request['body'] = {'mode': 'raw', 'raw': json.dumps(body, ensure_ascii=False, indent=2)}

    if tests:
        request['event'] = [{
            'listen': 'test',
            'script': {
                'exec': tests,
                'type': 'text/javascript'
            }
        }]

    return request


collection = {
    'info': {
        'name': 'Portfolio Microservices API',
        'description': 'Colección para probar los microservicios Laravel: auth-service, users-service, notifications-service y api-gateway.',
        'schema': 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
    },
    'variable': [
        {'key': 'gateway_url', 'value': 'http://127.0.0.1:8000', 'type': 'string'},
        {'key': 'auth_service_url', 'value': 'http://127.0.0.1:8001', 'type': 'string'},
        {'key': 'users_service_url', 'value': 'http://127.0.0.1:8002', 'type': 'string'},
        {'key': 'notifications_service_url', 'value': 'http://127.0.0.1:8003', 'type': 'string'},
        {'key': 'auth_token', 'value': '', 'type': 'string'}
    ],
    'item': [
        {
            'name': 'Auth Service',
            'item': [
                {'name': 'Health', 'request': make_request('GET', '{{auth_service_url}}/api/health', 8001)},
                {'name': 'Register', 'request': make_request('POST', '{{auth_service_url}}/api/v1/auth/register', 8001, {
                    'name': 'Ana García',
                    'email': 'ana@ejemplo.com',
                    'password': 'Secret123'
                }, [
                    'const json = pm.response.json();',
                    "if (json && json.data && json.data.token) {",
                    "  pm.collectionVariables.set('auth_token', json.data.token);",
                    '}'
                ])},
                {'name': 'Login', 'request': make_request('POST', '{{auth_service_url}}/api/v1/auth/login', 8001, {
                    'email': 'ana@ejemplo.com',
                    'password': 'Secret123'
                }, [
                    'const json = pm.response.json();',
                    "if (json && json.data && json.data.token) {",
                    "  pm.collectionVariables.set('auth_token', json.data.token);",
                    '}'
                ])}
            ]
        },
        {
            'name': 'Users Service',
            'item': [
                {'name': 'Health', 'request': make_request('GET', '{{users_service_url}}/api/health', 8002)},
                {'name': 'Dashboard', 'request': make_request('GET', '{{users_service_url}}/api/v1/dashboard', 8002)},
                {'name': 'List Clients', 'request': make_request('GET', '{{users_service_url}}/api/v1/clients', 8002)},
                {'name': 'Create Client', 'request': make_request('POST', '{{users_service_url}}/api/v1/clients', 8002, {
                    'name': 'María López',
                    'email': 'maria@empresa.com',
                    'company': 'Empresa ACME',
                    'status': 'active'
                })},
                {'name': 'List Projects', 'request': make_request('GET', '{{users_service_url}}/api/v1/projects', 8002)},
                {'name': 'Create Project', 'request': make_request('POST', '{{users_service_url}}/api/v1/projects', 8002, {
                    'client_id': 1,
                    'name': 'Portal Corporativo',
                    'status': 'in_progress',
                    'budget': 15000.5,
                    'due_date': '2026-10-30'
                })},
                {'name': 'List Tasks', 'request': make_request('GET', '{{users_service_url}}/api/v1/tasks', 8002)},
                {'name': 'Create Task', 'request': make_request('POST', '{{users_service_url}}/api/v1/tasks', 8002, {
                    'project_id': 1,
                    'title': 'Definir cronograma',
                    'assignee': 'Ana García',
                    'priority': 'high',
                    'status': 'pending'
                })}
            ]
        },
        {
            'name': 'Notifications Service',
            'item': [
                {'name': 'Health', 'request': make_request('GET', '{{notifications_service_url}}/api/health', 8003)},
                {'name': 'List Notifications', 'request': make_request('GET', '{{notifications_service_url}}/api/v1/notifications', 8003)},
                {'name': 'Send Contact Message', 'request': make_request('POST', '{{notifications_service_url}}/api/v1/contact', 8003, {
                    'to': 'cliente@empresa.com',
                    'subject': 'Consulta desde portafolio',
                    'message': 'Necesito más información sobre el proyecto.',
                    'status': 'queued',
                    'channel': 'email'
                })}
            ]
        },
        {
            'name': 'API Gateway',
            'item': [
                {'name': 'Gateway Health', 'request': make_request('GET', '{{gateway_url}}/api/health', 8000)},
                {'name': 'Gateway Register', 'request': make_request('POST', '{{gateway_url}}/api/v1/auth/register', 8000, {
                    'name': 'Ana García',
                    'email': 'ana@ejemplo.com',
                    'password': 'Secret123'
                })},
                {'name': 'Gateway Login', 'request': make_request('POST', '{{gateway_url}}/api/v1/auth/login', 8000, {
                    'email': 'ana@ejemplo.com',
                    'password': 'Secret123'
                })},
                {'name': 'Gateway Dashboard', 'request': make_request('GET', '{{gateway_url}}/api/v1/dashboard', 8000)},
                {'name': 'Gateway List Clients', 'request': make_request('GET', '{{gateway_url}}/api/v1/clients', 8000)},
                {'name': 'Gateway Create Client', 'request': make_request('POST', '{{gateway_url}}/api/v1/clients', 8000, {
                    'name': 'María López',
                    'email': 'maria@empresa.com',
                    'company': 'Empresa ACME',
                    'status': 'active'
                })},
                {'name': 'Gateway List Projects', 'request': make_request('GET', '{{gateway_url}}/api/v1/projects', 8000)},
                {'name': 'Gateway Create Project', 'request': make_request('POST', '{{gateway_url}}/api/v1/projects', 8000, {
                    'client_id': 1,
                    'name': 'Portal Corporativo',
                    'status': 'in_progress',
                    'budget': 15000.5,
                    'due_date': '2026-10-30'
                })},
                {'name': 'Gateway List Tasks', 'request': make_request('GET', '{{gateway_url}}/api/v1/tasks', 8000)},
                {'name': 'Gateway Create Task', 'request': make_request('POST', '{{gateway_url}}/api/v1/tasks', 8000, {
                    'project_id': 1,
                    'title': 'Definir cronograma',
                    'assignee': 'Ana García',
                    'priority': 'high',
                    'status': 'pending'
                })},
                {'name': 'Gateway List Notifications', 'request': make_request('GET', '{{gateway_url}}/api/v1/notifications', 8000)},
                {'name': 'Gateway Send Contact Message', 'request': make_request('POST', '{{gateway_url}}/api/v1/contact', 8000, {
                    'to': 'cliente@empresa.com',
                    'subject': 'Consulta desde portafolio',
                    'message': 'Necesito más información sobre el proyecto.',
                    'status': 'queued',
                    'channel': 'email'
                })}
            ]
        }
    ]
}

(base / 'portfolio-microservices.postman_collection.json').write_text(
    json.dumps(collection, ensure_ascii=False, indent=2),
    encoding='utf-8'
)

environment = {
    'id': '72f0440f-3218-43d3-b9ff-3a8f8aaf7d9a',
    'name': 'Portfolio Microservices - Local',
    'values': [
        {'key': 'gateway_url', 'value': 'http://127.0.0.1:8000', 'type': 'default', 'enabled': True},
        {'key': 'auth_service_url', 'value': 'http://127.0.0.1:8001', 'type': 'default', 'enabled': True},
        {'key': 'users_service_url', 'value': 'http://127.0.0.1:8002', 'type': 'default', 'enabled': True},
        {'key': 'notifications_service_url', 'value': 'http://127.0.0.1:8003', 'type': 'default', 'enabled': True},
        {'key': 'auth_token', 'value': '', 'type': 'secret', 'enabled': True},
    ],
    '_postman_variable_scope': 'environment',
    '_postman_exported_at': '2026-08-30T00:00:00.000Z',
    '_postman_exported_using': 'Postman'
}

(base / 'portfolio-microservices.postman_environment.json').write_text(
    json.dumps(environment, ensure_ascii=False, indent=2),
    encoding='utf-8'
)

print('Generated collection and environment successfully')
