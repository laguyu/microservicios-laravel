# Postman para los microservicios

Esta carpeta incluye la colección y el entorno para probar la API de los microservicios Laravel del portafolio.

## Archivos

- `portfolio-microservices.postman_collection.json` : colección con todos los endpoints.
- `portfolio-microservices.postman_environment.json` : variables locales para `gateway_url`, `auth_service_url`, `users_service_url` y `notifications_service_url`.

## Cómo importarlo en Postman

1. Abre Postman.
2. Haz clic en `Import`.
3. Importa el archivo `portfolio-microservices.postman_collection.json`.
4. Importa el archivo `portfolio-microservices.postman_environment.json`.
5. Selecciona el entorno `Portfolio Microservices - Local`.
6. Asegúrate de que los servicios estén corriendo en:
   - Auth: `http://127.0.0.1:8001`
   - Users: `http://127.0.0.1:8002`
   - Notifications: `http://127.0.0.1:8003`
   - Gateway: `http://127.0.0.1:8000`

## Orden recomendado para probar

### Flujo manual

1. `Auth Service > Register`
2. `Auth Service > Login`
3. `Users Service > Create Client`
4. `Users Service > Create Project`
5. `Users Service > Create Task`
6. `Notifications Service > Send Contact Message`
7. `API Gateway` para verificar el paso por el gateway

### Flujo automatizado

Usa la carpeta `Flujo completo del portafolio` para ejecutar la secuencia real del negocio:

1. Registrar usuario
2. Iniciar sesión
3. Listar clientes
4. Crear cliente
5. Listar proyectos
6. Crear proyecto
7. Crear tarea
8. Enviar mensaje de contacto

Las variables `auth_token`, `client_id` y `project_id` se actualizan automáticamente en cada paso para que el flujo funcione sin tocar manualmente los IDs.

## Correr los servicios localmente

```bash
cd services/auth-service && php artisan serve --host=127.0.0.1 --port=8001
cd services/users-service && php artisan serve --host=127.0.0.1 --port=8002
cd services/notifications-service && php artisan serve --host=127.0.0.1 --port=8003
cd services/api-gateway && php artisan serve --host=127.0.0.1 --port=8000
```

## Nota

Los endpoints de cada servicio están definidos bajo `api/v1/...` y el `api-gateway` reenvía las peticiones a cada microservicio usando las variables de entorno `AUTH_SERVICE_URL`, `USERS_SERVICE_URL` y `NOTIFICATIONS_SERVICE_URL`.
