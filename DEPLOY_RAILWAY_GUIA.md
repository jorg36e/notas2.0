# 🚀 Guía Completa: Deploy Laravel en Railway.app

## PASO 1: INSTALAR GIT (5 minutos)

### Opción A: Descargar e Instalar
1. Ve a: https://git-scm.com/download/win
2. Descarga el instalador
3. Ejecuta el instalador (deja todas las opciones por defecto)
4. Reinicia VS Code después de instalar

### Opción B: Con winget (si tienes Windows 11)
```powershell
winget install Git.Git
```

---

## PASO 2: CONFIGURAR GIT (2 minutos)

Después de instalar Git, ejecuta estos comandos:

```powershell
git config --global user.name "Tu Nombre"
git config --global user.email "tu-email@ejemplo.com"
```

---

## PASO 3: INICIALIZAR REPOSITORIO GIT (2 minutos)

En la terminal de VS Code, ejecuta:

```powershell
# Inicializar repositorio
git init

# Agregar todos los archivos
git add .

# Hacer el primer commit
git commit -m "Initial commit - NOTAS 2.0"
```

---

## PASO 4: CREAR REPOSITORIO EN GITHUB (3 minutos)

1. Ve a: https://github.com/new
2. Nombre del repositorio: `notas2.0` o el que prefieras
3. Déjalo **PRIVADO** (tu código no será público)
4. **NO marques** "Initialize repository"
5. Clic en "Create repository"

---

## PASO 5: CONECTAR CON GITHUB (2 minutos)

GitHub te mostrará comandos. Ejecuta estos:

```powershell
# Cambiar rama a main
git branch -M main

# Agregar remote
git remote add origin https://github.com/TU-USUARIO/notas2.0.git

# Subir código
git push -u origin main
```

**Nota:** Te pedirá autenticación. Usa un Personal Access Token:
- Ve a: https://github.com/settings/tokens
- "Generate new token (classic)"
- Marca: `repo` (todos los permisos de repo)
- Copia el token y úsalo como contraseña

---

## PASO 6: CREAR CUENTA EN RAILWAY (2 minutos)

1. Ve a: https://railway.app/
2. Clic en "Login"
3. Usa "Login with GitHub" (más fácil)
4. Autoriza Railway a acceder a GitHub

---

## PASO 7: CREAR PROYECTO EN RAILWAY (3 minutos)

1. En Railway Dashboard, clic en "New Project"
2. Selecciona "Deploy from GitHub repo"
3. Busca y selecciona tu repositorio `notas2.0`
4. Railway detectará automáticamente que es Laravel

---

## PASO 8: AGREGAR MYSQL DATABASE (2 minutos)

En tu proyecto de Railway:

1. Clic en "+ New" (botón superior derecho)
2. Selecciona "Database"
3. Selecciona "Add MySQL"
4. Railway creará la base de datos automáticamente

---

## PASO 9: CONFIGURAR VARIABLES DE ENTORNO (5 minutos)

En tu proyecto Railway:

1. Clic en tu servicio Laravel (no en MySQL)
2. Ve a la pestaña "Variables"
3. Clic en "RAW Editor"
4. Copia y pega esto:

```env
APP_NAME="NOTAS 2.0"
APP_ENV=production
APP_DEBUG=false
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

APP_LOCALE=es
APP_FALLBACK_LOCALE=es

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=database
SESSION_LIFETIME=120

FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@notas2.app"
MAIL_FROM_NAME="NOTAS 2.0"
```

5. **IMPORTANTE:** Necesitas generar APP_KEY:
   - En otra pestaña de variables, agrega:
   - Variable: `APP_KEY`
   - Valor: Lo generaremos después

---

## PASO 10: GENERAR APP_KEY (2 minutos)

En tu computadora, ejecuta:

```powershell
php artisan key:generate --show
```

Copia el valor que aparece (algo como `base64:xxxxx...`) y agrégalo como variable `APP_KEY` en Railway.

---

## PASO 11: CONFIGURAR DOMINIO PÚBLICO (1 minuto)

1. En tu servicio Laravel en Railway
2. Ve a "Settings"
3. En "Networking" → "Public Networking"
4. Clic en "Generate Domain"
5. Railway te dará una URL como: `tu-app.up.railway.app`

---

## PASO 12: HACER DEPLOY (1 minuto)

Railway desplegará automáticamente cuando:
- Subas cambios a GitHub
- Cambies variables de entorno
- Presiones "Deploy" manualmente

El primer deploy puede tomar 3-5 minutos.

---

## PASO 13: MIGRAR BASE DE DATOS (IMPORTANTE)

### Opción A: Migraciones automáticas
Railway ya ejecutará `php artisan migrate --force` automáticamente.

### Opción B: Exportar/Importar datos existentes

Si ya tienes datos en tu base de datos local:

1. Exporta tu BD local:
```powershell
# En tu computadora
cd C:\xampp\mysql\bin
.\mysqldump.exe -u root -p if0_40913984_notas2 > notas_backup.sql
```

2. En Railway:
   - Clic en tu servicio MySQL
   - Ve a "Data" o "Connect"
   - Usa las credenciales para conectar con MySQL Workbench o phpMyAdmin
   - Importa el archivo `notas_backup.sql`

---

## PASO 14: VERIFICAR DEPLOY (2 minutos)

1. Ve a la URL que Railway te dio
2. Deberías ver tu aplicación funcionando
3. Prueba login con un usuario de prueba

---

## 🔧 COMANDOS ÚTILES

### Ver logs en Railway:
- En tu servicio → pestaña "Deployments"
- Clic en el deploy activo
- Verás logs en tiempo real

### Ejecutar comandos en Railway:
Railway no tiene terminal SSH, pero puedes:
- Agregar comandos al `railway.json`
- Usar GitHub Actions para tareas complejas

### Subir cambios:
```powershell
git add .
git commit -m "Descripción del cambio"
git push
```
Railway desplegará automáticamente.

---

## ⚠️ TROUBLESHOOTING

### Error: "Application not found"
- Verifica que `APP_KEY` esté configurado
- Revisa los logs del deploy

### Error de conexión a BD
- Verifica que las variables `DB_*` apunten a `${{MySQL.VARIABLE}}`
- Railway conecta automáticamente los servicios

### Error 500
- Activa `APP_DEBUG=true` temporalmente
- Revisa logs en Railway

### Storage/uploads no funcionan
- Railway tiene almacenamiento efímero
- Para archivos permanentes, usa S3 o Cloudinary (gratis)

---

## 💰 COSTOS

Railway te da **$5 USD de crédito gratis al mes**:
- Suficiente para un proyecto pequeño/mediano
- ~500 horas de ejecución
- Tráfico ilimitado

Si se acaba:
- $0.000231/min de ejecución (~$10/mes)
- O pausa el proyecto cuando no lo uses

---

## 📱 PRÓXIMOS PASOS RECOMENDADOS

1. **Dominio personalizado** (opcional):
   - Compra un dominio en Namecheap/GoDaddy
   - Conéctalo en Railway Settings → Domains

2. **Configurar almacenamiento permanente**:
   - Cloudinary (gratis) para imágenes
   - AWS S3 (gratis 1 año) para archivos

3. **Monitoreo**:
   - Railway tiene métricas incluidas
   - Puedes agregar Sentry para errores

---

## ❓ ¿NECESITAS AYUDA?

Si encuentras algún error durante el proceso:
1. Copia el error completo
2. Revisa los logs en Railway
3. Pregúntame y te ayudo a solucionarlo

---

**¡Listo! Tu proyecto estará en línea y accesible desde cualquier lugar 🎉**
