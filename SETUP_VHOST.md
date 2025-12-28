# Configuración de Virtual Host para XAMPP

## Opción 1: Acceso Rápido (Sin Virtual Host)

Simplemente usa la URL completa:
```
http://localhost/newprofesional/public/
```

**Credenciales de acceso:**
- Usuario: `admin`
- Contraseña: `admin123`

---

## Opción 2: Virtual Host (Recomendado para desarrollo)

Esto te permitirá usar: `http://colegio.local` en lugar de `http://localhost/newprofesional/public/`

### Paso 1: Editar httpd-vhosts.conf

**Ubicación:** `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Agrega al final del archivo:

```apache
# Virtual Host para Colegio Profesional
<VirtualHost *:80>
    ServerName colegio.local
    DocumentRoot "C:/xampp/htdocs/newprofesional/public"

    <Directory "C:/xampp/htdocs/newprofesional/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/colegio-error.log"
    CustomLog "logs/colegio-access.log" common
</VirtualHost>
```

### Paso 2: Editar archivo hosts

**Ubicación:** `C:\Windows\System32\drivers\etc\hosts`

**IMPORTANTE:** Ábrelo como Administrador (click derecho → Ejecutar como administrador)

Agrega esta línea al final:

```
127.0.0.1    colegio.local
```

### Paso 3: Habilitar Virtual Hosts en Apache

Edita: `C:\xampp\apache\conf\httpd.conf`

Busca esta línea y asegúrate de que NO esté comentada (sin #):

```apache
Include conf/extra/httpd-vhosts.conf
```

### Paso 4: Reiniciar Apache

1. Abre XAMPP Control Panel
2. Detén Apache
3. Inicia Apache nuevamente

### Paso 5: Acceder

Ahora puedes acceder en:
```
http://colegio.local
```

---

## Opción 3: .htaccess en la raíz (Rápido)

Si solo quieres redireccionar desde la raíz del proyecto a `/public/`:

Crea un archivo `.htaccess` en: `C:\xampp\htdocs\newprofesional\.htaccess`

Con este contenido:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^$ public/ [L]
    RewriteRule ^((?!public/).*)$ public/$1 [L,NC]
</IfModule>
```

Esto permitirá usar: `http://localhost/newprofesional/` (sin /public/)

---

## Verificación Rápida

Después de cualquier opción, verifica:

1. ✅ Puedes acceder a la URL
2. ✅ Ves el formulario de login
3. ✅ Login con admin/admin123 funciona
4. ✅ Ves el dashboard con estadísticas

---

## Credenciales por Defecto

**Usuario Administrador:**
- Username: `admin`
- Password: `admin123`
- Email: `admin@colegio.pe`

**Cambiar contraseña:** Ve a tu perfil después de iniciar sesión.

---

## URLs del Sistema

Después de login, puedes acceder a:

- 📊 Dashboard: `/dashboard`
- 👥 Personas: `/personas`
- 🎓 Colegiados: `/colegiados`
- 💰 Aportaciones: `/aportaciones`
- 💳 Caja: `/caja`
- 📈 Reportes: `/reportes`
- 👤 Perfil: `/auth/profile`

---

## Troubleshooting

**Error 404 en todas las páginas:**
- Verifica que mod_rewrite esté habilitado
- Revisa el archivo `.htaccess` en `/public/`

**CSS no carga:**
- Verifica la constante APP_URL en `config/config.php`
- Debe coincidir con tu URL de acceso

**Sesión no persiste:**
- Verifica permisos del directorio `logs/`
- Asegúrate de que PHP pueda escribir sesiones
