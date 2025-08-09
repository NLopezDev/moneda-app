# Conversor de Monedas

Una aplicación web simple y moderna para convertir monedas en tiempo real.

## 🚀 Características

- **Conversión en tiempo real** usando tasas de cambio actualizadas
- **Interfaz moderna y responsive** con diseño limpio
- **Soporte para múltiples monedas** (USD, EUR, ARS, BRL, CLP, UYU, MXN, COP, GBP, JPY, CHF, CAD, AUD, CNY, HKD, INR, RUB, ZAR, KRW, SGD, NZD)
- **Caché local** para mejorar el rendimiento (30 minutos)
- **Intercambio rápido** entre monedas
- **Sin API keys** - usa proveedores gratuitos

## 🛠️ Tecnologías

- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Backend:** PHP 8.2+
- **Servidor:** Apache (XAMPP)
- **API:** exchangerate-api.com (gratuito)

## 📁 Estructura del Proyecto

```
moneda-app/
├── api/
│   ├── convert.php          # API de conversión
│   └── .cache/              # Directorio de caché (ignorado por Git)
├── public/
│   ├── index.html           # Página principal
│   ├── script.js            # Lógica del frontend
│   └── styles.css           # Estilos CSS
├── .gitignore               # Archivos ignorados por Git
└── README.md               # Este archivo
```

## 🚀 Instalación

### Requisitos
- PHP 8.0 o superior
- Apache/Nginx
- XAMPP (recomendado para desarrollo)

### Pasos

1. **Clona el repositorio:**
   ```bash
   git clone https://github.com/tu-usuario/moneda-app.git
   cd moneda-app
   ```

2. **Configura el servidor web:**
   - Copia el proyecto a tu directorio web (ej: `htdocs/` en XAMPP)
   - O configura un virtual host

3. **Configura permisos:**
   ```bash
   mkdir -p api/.cache
   chmod 775 api/.cache
   ```

4. **Accede a la aplicación:**
   ```
   http://localhost/moneda-app/public/
   ```

## 📖 Uso

1. Abre la aplicación en tu navegador
2. Ingresa el monto a convertir
3. Selecciona la moneda origen
4. Selecciona la moneda destino
5. Haz clic en "Convertir"
6. Usa el botón "↔️ Invertir" para cambiar rápidamente las monedas

## 🔧 API

### Endpoint
```
GET /api/convert.php?amount={monto}&from={moneda_origen}&to={moneda_destino}
```

### Ejemplo
```bash
curl "http://localhost/moneda-app/api/convert.php?amount=100&from=USD&to=ARS"
```

### Respuesta
```json
{
  "converted": 132942,
  "rate": 1329.42,
  "provider": "exchangerate-api.com",
  "ts": 1754759830555
}
```

## 🎨 Personalización

### Agregar nuevas monedas
Edita el array `$ALLOWED` en `api/convert.php` y `CURRENCIES` en `public/script.js`.

### Cambiar proveedor de tasas
Modifica la URL en la función `fetchRate` de `api/convert.php`.

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🙏 Agradecimientos

- [exchangerate-api.com](https://www.exchangerate-api.com/) por proporcionar las tasas de cambio gratuitas
- La comunidad de desarrolladores PHP y JavaScript

## 📞 Soporte

Si tienes alguna pregunta o problema, abre un issue en GitHub.
