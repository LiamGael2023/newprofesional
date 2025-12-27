# Guía de Solución de Problemas

## Error: "Connection refused" o "No se puede establecer una conexión"

### Causa
MySQL no está corriendo o no está escuchando en el puerto configurado.

### Solución

#### 1️⃣ **Iniciar MySQL**

**Si usas XAMPP:**
1. Abre el Panel de Control de XAMPP
2. Click en "Start" en la fila de MySQL
3. Espera a que el botón cambie a verde y diga "Stop"

**Si usas WAMP:**
1. Abre WAMP
2. Click en el icono de la bandeja del sistema
3. Selecciona "Start All Services"
4. El icono debe ponerse verde

**Si usas Laragon:**
1. Abre Laragon
2. Click en "Start All"

**Si usas MySQL standalone (Windows):**
```cmd
net start mysql80
```

**Si usas MySQL standalone (Linux/Mac):**
```bash
sudo systemctl start mysql
# o
sudo service mysql start
```

#### 2️⃣ **Verificar el Puerto de MySQL**

**Para XAMPP/WAMP:**
1. Abre el archivo de configuración `my.ini`:
   - XAMPP: `C:\xampp\mysql\bin\my.ini`
   - WAMP: `C:\wamp64\bin\mysql\mysql8.x.x\my.ini`

2. Busca la línea que dice `port=` (generalmente está en la sección `[mysqld]`)
   ```ini
   [mysqld]
   port=3306
   ```

3. Anota el número de puerto (3306, 3307, etc.)

**Usando phpMyAdmin:**
1. Abre phpMyAdmin
2. Ve a la pestaña "Variables"
3. Busca `port`

**Usando línea de comandos:**
```bash
mysql -u root -p -e "SHOW VARIABLES LIKE 'port';"
```

#### 3️⃣ **Actualizar la Configuración del Sistema**

Edita `config/config.php`:

```php
define('DB_HOST', '127.0.0.1'); // Usar 127.0.0.1, NO localhost
define('DB_PORT', '3306'); // Cambiar al puerto correcto (3306, 3307, etc.)
define('DB_USER', 'root');
define('DB_PASS', ''); // Agregar contraseña si la tienes
```

**IMPORTANTE:**
- Usa `127.0.0.1` en lugar de `localhost` para forzar conexión TCP/IP
- El puerto más común es `3306`, pero en XAMPP con Apache puede ser `3307`

#### 4️⃣ **Verificar la Conexión**

Ejecuta el script de prueba:
```bash
php test_connection.php
```

Si ves "✓ Conexión exitosa!", estás listo para continuar.

#### 5️⃣ **Crear la Base de Datos**

Una vez que MySQL esté corriendo:

**Opción A - Usando línea de comandos:**
```bash
# Para puerto 3306
mysql -u root -p < database/schema.sql

# Para puerto 3307
mysql -u root -p --port=3307 < database/schema.sql
```

**Opción B - Usando phpMyAdmin:**
1. Abre phpMyAdmin (http://localhost/phpmyadmin)
2. Click en "Importar"
3. Selecciona el archivo `database/schema.sql`
4. Click en "Continuar"

**Opción C - Crear manualmente:**
1. Abre phpMyAdmin
2. Click en "Nueva" en el panel izquierdo
3. Nombre: `colegio_profesional`
4. Cotejamiento: `utf8mb4_unicode_ci`
5. Click en "Crear"
6. Luego ve a la pestaña "Importar" y sube `database/schema.sql`

## Verificar que todo funciona

### Probar conexión:
```bash
php check_mysql.php
```

### Probar conexión completa:
```bash
php test_connection.php
```

### Acceder al sistema:
1. Asegúrate de que Apache también está corriendo
2. Abre el navegador: `http://localhost/newprofesional/public/`
3. Usuario: `admin`
4. Contraseña: `admin123`

## Errores Comunes

### "Access denied for user 'root'@'localhost'"
**Solución:** Actualiza `DB_PASS` en `config/config.php` con tu contraseña de MySQL.

### "Unknown database 'colegio_profesional'"
**Solución:** Importa el schema SQL como se indica arriba.

### "Call to undefined function mysql_connect()"
**Solución:** Asegúrate de tener PHP 8.1+ con extensión PDO MySQL habilitada.

### Página en blanco o error 500
**Solución:**
1. Verifica que `mod_rewrite` esté habilitado en Apache
2. Revisa el archivo `logs/error.log`
3. Asegúrate de que la carpeta `logs` tenga permisos de escritura

## Contacto
Si sigues teniendo problemas, revisa el archivo `logs/error.log` para más detalles.
