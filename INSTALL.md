# 🚀 Guía de Instalación Rápida

## Requisitos Previos

- ✅ PHP 8.1 o superior
- ✅ MySQL 8.0 o superior (o MariaDB)
- ✅ Apache con mod_rewrite habilitado
- ✅ XAMPP, WAMP, Laragon u otro servidor local

## Instalación en 3 Pasos

### 📌 Paso 1: Iniciar MySQL

**XAMPP:**
- Abre el Panel de Control de XAMPP
- Click en "Start" junto a Apache y MySQL
- Espera a que ambos se pongan verdes

**WAMP:**
- Inicia WAMP
- El icono debe ponerse verde

**Laragon:**
- Abre Laragon
- Click en "Start All"

---

### 📌 Paso 2: Configurar Conexión

1. **Verificar el puerto de MySQL:**

   Ejecuta desde la carpeta del proyecto:
   ```bash
   php check_mysql.php
   ```

   Esto te dirá en qué puerto está MySQL (normalmente 3306 o 3307)

2. **Editar configuración:**

   Abre `config/config.php` y ajusta:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_PORT', '3306'); // Cambia al puerto que encontraste
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Tu contraseña de MySQL (vacía por defecto)
   ```

3. **Probar conexión:**
   ```bash
   php test_connection.php
   ```

   Si ves "✓ Conexión exitosa!", continúa al siguiente paso.

---

### 📌 Paso 3: Instalar Base de Datos

Elige **UNA** de estas opciones:

#### 🔷 Opción A: Instalador Web (Recomendado)

1. Abre en tu navegador:
   ```
   http://localhost/newprofesional/install_database.php
   ```

2. Click en "Instalar Base de Datos"

3. Espera a que termine (verás mensajes de confirmación)

4. **¡Listo!** Accede al sistema

#### 🔷 Opción B: phpMyAdmin

1. Abre phpMyAdmin: `http://localhost/phpmyadmin`

2. Click en "Importar"

3. Selecciona el archivo `database/schema.sql`

4. Click en "Continuar"

5. Espera a que complete la importación

#### 🔷 Opción C: Línea de Comandos

```bash
# Para puerto 3306
mysql -u root -p < database/schema.sql

# Para puerto 3307
mysql -u root -p --port=3307 < database/schema.sql
```

---

## 🎉 Acceder al Sistema

Una vez instalado, abre:

```
http://localhost/newprofesional/public/
```

**Credenciales por defecto:**
- 👤 Usuario: `admin`
- 🔑 Contraseña: `admin123`

---

## ✅ Verificación Post-Instalación

Ejecuta este comando para verificar que todo esté bien:

```bash
php verify_database.php
```

Deberías ver:
- ✓ Todas las tablas existen (10+)
- ✓ Usuario administrador encontrado
- ✓ Roles configurados
- ✓ Tipos de aportación configurados

---

## ❌ ¿Problemas?

### Error: "Connection refused"
**Solución:** MySQL no está corriendo. Inicia XAMPP/WAMP/Laragon.

### Error: "Access denied"
**Solución:** Verifica usuario y contraseña en `config/config.php`

### Error: "Unknown database"
**Solución:** La base de datos no se creó. Usa el instalador web o importa el SQL.

### Error: "Column not found: 'estado'"
**Solución:** La base de datos está incompleta. Ejecuta:
```bash
php install_database.php
```
O marca "Eliminar base de datos existente" en el instalador web.

### Más ayuda
Consulta el archivo `TROUBLESHOOTING.md` para soluciones detalladas.

---

## 🔒 Seguridad Post-Instalación

1. **Elimina los archivos de instalación:**
   ```bash
   rm install_database.php
   rm verify_database.php
   rm check_mysql.php
   rm test_connection.php
   ```

2. **Cambia la contraseña del administrador:**
   - Inicia sesión en el sistema
   - Ve a "Mi Perfil"
   - Cambia la contraseña

3. **Configura APP_ENV en producción:**
   ```php
   define('APP_ENV', 'production');
   ```

---

## 📚 Próximos Pasos

1. **Crear usuarios adicionales** (recomendado)
2. **Registrar personas** en el sistema
3. **Convertir personas a colegiados**
4. **Generar cuotas mensuales** automáticamente
5. **Procesar pagos** en caja

**Documentación completa:** Ver `README.md`

---

## 🆘 Soporte

Si tienes problemas:

1. Revisa `TROUBLESHOOTING.md`
2. Verifica los logs en `logs/error.log`
3. Ejecuta `php verify_database.php`
4. Revisa la configuración en `config/config.php`

---

**¡Disfruta del sistema!** 🎓
