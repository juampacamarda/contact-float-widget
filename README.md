# Contact Float Widget

Plugin de WordPress para crear un widget flotante de contacto totalmente configurable con soporte para WhatsApp, Email y enlaces personalizados.

## Características

- ✅ **Widget flotante** totalmente responsive
- ✅ **Shortcode**: `[contact_float_widget]`
- ✅ **Sin dependencias**: No usa Bootstrap ni jQuery (solo JavaScript vanilla)
- ✅ **Totalmente configurable** desde el panel de administración
- ✅ **Múltiples secciones** con items ilimitados
- ✅ **Soporte para**:
  - WhatsApp (con mensaje prellenado)
  - Email
  - Enlaces personalizados
- ✅ **Iconos personalizables** (imagen o SVG por defecto)
- ✅ **Importar/Exportar** configuración en JSON
- ✅ **Accesibilidad**: Soporta navegación por teclado y lectores de pantalla
- ✅ **Seguridad**: Todos los datos son sanitizados y validados

## Instalación

1. Subir la carpeta `contact-float-widget` al directorio `/wp-content/plugins/`
2. Activar el plugin desde el menú 'Plugins' en WordPress
3. Ir a 'Ajustes > Contact Float Widget' para configurar

## Uso

### Visualización automática (Por defecto)

Por defecto, el widget se muestra **automáticamente en todo el sitio**. No necesitas hacer nada más que configurarlo en los ajustes.

Si prefieres controlarlo manualmente, puedes desactivar la opción "Mostrar automáticamente" en los ajustes y usar el shortcode.

### Shortcode (Opcional)

Si desactivas la visualización automática, puedes insertar el shortcode en páginas específicas:

```
[contact_float_widget]
```

### Configuración

Acceder a **Ajustes > Contact Float Widget** para configurar:

#### Opciones generales:
- **Color principal**: Color del botón y títulos
- **Lado**: Derecha o Izquierda
- **Offset top**: Distancia desde la parte superior (px)
- **Mostrar abierto**: Si se muestra abierto al cargar la página
- **Mostrar automáticamente**: Si se muestra en todo el sitio (activado por defecto)

#### Secciones e Items:
- Crear secciones para organizar contactos
- Agregar items dentro de cada sección:
  - **WhatsApp**: Número en formato E.164 y mensaje prellenado
  - **Email**: Dirección de correo
  - **Link**: URL personalizada
- Opción de agregar icono personalizado para cada item

#### Importar/Exportar:
- Exportar configuración como JSON
- Importar configuración desde archivo JSON

## Estructura de archivos

```
contact-float-widget/
├── contact-float-widget.php    # Archivo principal del plugin
├── uninstall.php               # Script de desinstalación
├── README.md                   # Documentación
├── includes/
│   ├── admin.php               # Página de administración
│   ├── assets.php              # Gestión de CSS y JS
│   └── renderer.php            # Renderizado del widget
├── assets/
│   ├── css/
│   │   ├── widget.css          # Estilos del frontend
│   │   └── admin.css           # Estilos del admin
│   └── js/
│       ├── widget.js           # JavaScript del frontend
│       └── admin.js            # JavaScript del admin
└── languages/
    └── contact-float-widget.pot # Archivo de traducción
```

## Clases CSS (BEM)

El widget utiliza la metodología BEM para las clases CSS:

- `.cfw-widget` - Contenedor principal
- `.cfw-widget--right` / `.cfw-widget--left` - Modificadores de posición
- `.cfw-toggle` - Botón de apertura/cierre
- `.cfw-panel` - Panel desplegable
- `.cfw-section` - Sección de contactos
- `.cfw-item` - Item individual de contacto
- `.cfw-item__media` - Contenedor del icono
- `.cfw-item__text` - Contenedor del texto

## Funciones JavaScript

### Frontend (widget.js)
- `initWidget()` - Inicializa el widget
- `togglePanel()` - Alterna entre abierto/cerrado
- `closePanel()` - Cierra el panel
- Soporte para tecla Escape
- Cierre al hacer clic fuera

### Admin (admin.js)
- Agregar/eliminar secciones
- Agregar/eliminar items
- Selector de medios para iconos
- Validación del formulario
- Drag & drop para reordenar (opcional)

## Seguridad

- ✅ Todos los inputs son sanitizados con funciones de WordPress
- ✅ Outputs escapados con `esc_html()`, `esc_attr()`, `esc_url()`
- ✅ Nonces para todas las acciones del formulario
- ✅ Verificación de permisos `manage_options`
- ✅ Validación de tipos de archivos en importación

## Compatibilidad

- WordPress 5.0 o superior
- PHP 7.0 o superior
- Todos los navegadores modernos
- Responsive design

## Personalización

### CSS Personalizado

El widget usa variables CSS que pueden ser sobrescritas:

```css
.cfw-widget {
    --primary: #25d366; /* Color principal */
}
```

### Modificar traducciones

Editar el archivo `.pot` en la carpeta `languages/` o usar plugins como Loco Translate.

## Changelog

### 1.0.0
- Lanzamiento inicial
- Soporte para WhatsApp, Email y Links
- Sistema de secciones e items
- Importar/Exportar configuración
- JavaScript vanilla (sin jQuery en frontend)
- Diseño responsive

## Autor

Juampa Camarda

## Licencia

GPL v2 or later

## Soporte

Para reportar bugs o solicitar características, visita el [repositorio del plugin](https://github.com/tu-usuario/contact-float-widget).
