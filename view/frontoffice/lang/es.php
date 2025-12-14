<?php
return [
    // Sitio general
    'site_title' => 'GameForGood',
    'home' => 'Inicio',
    'live_stats' => 'Estadísticas en Vivo',
    'admin_area' => 'Área de Administración',
    'copyright' => '© 2024 GameForGood - Todos los derechos reservados.',
    'tagline' => 'Juega por una buena causa',
    
    // Sección héroe
    'game_for_good' => 'GameForGood',
    'welcome' => 'Bienvenido a nuestra plataforma de donaciones dedicada a organizaciones benéficas',
    'slogan' => 'Tu juego puede cambiar el mundo',
    'support_cause' => 'Apoyar una causa',
    'see_live_stats' => 'Ver estadísticas en vivo',
    
    // Organizaciones
    'our_partners' => 'Nuestros Socios',
    'choose_cause' => 'Elige una causa que te importe y dona ahora',
    'progress' => 'Progreso',
    'collected' => 'recaudado',
    'goal' => 'Objetivo',
    'remaining' => 'Restante',
    'to_collect' => 'por recaudar',
    'goal_reached' => '¡Objetivo alcanzado!',
    'make_donation' => 'Hacer una donación',
    'visit_website' => 'Visitar sitio web',
    
    // Donaciones
    'donations' => 'Donaciones',
    'donors' => 'Donantes',
    'last_donation' => 'Última donación',
    'donation_success' => '¡Gracias por tu donación!',
    
    // Idioma y Moneda
    'current_language' => 'Idioma actual',
    'currency' => 'Moneda',
    'currency_symbol' => '€',
    'currency_code' => 'EUR',
    'decimal_separator' => ',',
    'thousands_separator' => '.',
    
    // Etiquetas de formulario
    'name' => 'Nombre',
    'email' => 'Correo electrónico',
    'amount' => 'Cantidad',
    'message' => 'Mensaje (opcional)',
    'submit' => 'Enviar',
    'select_organisation' => 'Seleccionar organización',
    
    // Mensajes
    'required_field' => 'Este campo es obligatorio',
    'invalid_email' => 'Correo electrónico inválido',
    'min_amount' => 'La cantidad mínima es {amount}',
    'thank_you' => 'Gracias por tu generosidad',
    'donation_recorded' => 'Tu donación ha sido registrada con éxito',
    
    // Formas plurales
    'donor_one' => '{count} donante',
    'donor_many' => '{count} donantes',
    'donation_one' => '{count} donación',
    'donation_many' => '{count} donaciones',
    'euro_one' => '{count} euro',
    'euro_many' => '{count} euros',
    
    // Estadísticas
    'total_donations' => 'Total de donaciones',
    'total_donors' => 'Total de donantes',
    'average_donation' => 'Donación promedio',
    'top_donor' => 'Mejor donante',
    'recent_donations' => 'Donaciones recientes',
    'live_updates' => 'Actualizaciones en vivo',
    
    // Tiempo
    'just_now' => 'Ahora mismo',
    'minutes_ago' => 'Hace {count} minuto(s)',
    'hours_ago' => 'Hace {count} hora(s)',
    'days_ago' => 'Hace {count} día(s)',
    
    // Meses (para formatDate)
    'months' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
    
    // Días (para formatDate)
    'days' => ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'],
    
    // Formulario de donación específico
    'make_donation' => 'Hacer una Donación',
    'make_difference' => 'Tu donación marca la diferencia',
    'error_no_org_selected' => 'Ninguna organización seleccionada. Por favor, elige una organización desde la página de inicio.',
    'donor_lastname' => 'Apellido del donante',
    'placeholder_lastname' => 'Tu apellido',
    'donor_firstname' => 'Nombre del donante',
    'placeholder_firstname' => 'Tu nombre',
    'donor_email' => 'Email del donante',
    'optional' => 'opcional',
    'donation_amount' => 'Cantidad de la donación',
    'custom_amount' => 'Otra cantidad',
    'donation_date' => 'Fecha de la donación',
    'date_auto_today' => 'Fecha automática (hoy)',
    'donation_type' => 'Tipo de donación',
    'select_type' => 'Seleccionar tipo',
    'monetary' => 'Monetaria',
    'material' => 'Material',
    'back_home' => 'Volver al inicio',
    'complete_donation' => 'Completar donación',
    'secure_payment' => 'Pago seguro',
    'data_protected' => 'Datos protegidos',
    'back_to_home' => 'Volver al inicio',
    'back_to_home_to_select' => 'Volver al inicio para elegir',
    'processing' => 'Procesando',
    
    // Mensajes de error
    'error_amount_zero' => 'La cantidad debe ser mayor que cero',
    'error_invalid_date' => 'Fecha inválida',
    'error_date_today' => 'La fecha debe ser la de hoy',
    'error_donation_type' => 'Por favor, selecciona un tipo de donación',
    'error_select_org' => 'Por favor, selecciona una organización',
    'error_donor_name' => 'El apellido es requerido',
    'error_invalid_name' => 'Apellido inválido (caracteres especiales no permitidos)',
    'error_name_min_length' => 'El apellido debe tener al menos 2 caracteres',
    'error_name_max_length' => 'El apellido no puede exceder 50 caracteres',
    'error_donor_firstname' => 'El nombre es requerido',
    'error_invalid_firstname' => 'Nombre inválido (caracteres especiales no permitidos)',
    'error_firstname_min_length' => 'El nombre debe tener al menos 2 caracteres',
    'error_firstname_max_length' => 'El nombre no puede exceder 50 caracteres',
    'error_invalid_email' => 'Dirección de email inválida',
    'donation_error' => 'Ocurrió un error al registrar la donación',
    'validation_errors' => 'Errores de validación',
    'please_correct_errors' => 'Por favor, corrige los errores a continuación',
    'validation_please_correct' => 'Por favor, corrige los siguientes errores:',
    'error_occurred' => 'Error',
     // Mensajes de Validación JavaScript
    'validation_required' => 'Este campo es obligatorio',
    'validation_min_length' => 'Debe tener al menos {min} caracteres',
    'validation_max_length' => 'No puede exceder {max} caracteres',
    'validation_invalid_email' => 'Dirección de email inválida',
    'validation_invalid_name' => 'Solo se permiten letras, espacios, guiones y apóstrofes',
    'validation_invalid_amount' => 'Cantidad inválida',
    'validation_amount_min' => 'La cantidad mínima es {min}',
    'validation_amount_max' => 'La cantidad máxima es {max}',
    'validation_required_selection' => 'Por favor, haga una selección',
    'validation_processing' => 'Procesando...',
    
    // Mensajes específicos de campo
    'validation_lastname_required' => 'El apellido es obligatorio',
    'validation_firstname_required' => 'El nombre es obligatorio',
    'validation_amount_required' => 'La cantidad es obligatoria',
    'validation_type_required' => 'El tipo de donación es obligatorio',
    'validation_date_required' => 'La fecha es obligatoria',
    
    // Mensajes de éxito
    'validation_field_valid' => 'Campo válido',
    'validation_form_valid' => 'Formulario válido',
    
    // Contadores de caracteres
    'characters' => 'caracteres',
    'characters_remaining' => 'caracteres restantes',
    
    // Botones de cantidad
    'amount_10' => '10 €',
    'amount_25' => '25 €',
    'amount_50' => '50 €',
    'amount_100' => '100 €',
    'amount_250' => '250 €',
    'amount_500' => '500 €',
    
    // Actualizaciones de placeholder
    'placeholder_select_type' => 'Seleccionar...',
    
    // Mensajes de confirmación
    'confirm_submission' => 'Confirmar envío',
    'confirm_submit_donation' => '¿Está seguro de que desea enviar esta donación?',
    'confirm_yes' => 'Sí, continuar',
    'confirm_no' => 'No, cancelar',
    
    // Estados de carga
    'loading' => 'Cargando...',
    'saving' => 'Guardando...',
    'redirecting' => 'Redirigiendo...',
    
    // Mensajes del navegador
    'browser_validation_email' => 'Por favor, introduzca una dirección de email válida',
    'browser_validation_required' => 'Por favor, rellene este campo',
    'browser_validation_pattern' => 'Formato incorrecto',
    
    // Tooltips
    'tooltip_name_format' => 'Solo letras, espacios, guiones (-) y apóstrofes (\')',
    'tooltip_email_optional' => 'Opcional - para el recibo',
    'tooltip_amount_quick' => 'Haga clic para seleccionar',
    'tooltip_amount_custom' => 'Introduzca cantidad personalizada',
    'tooltip_date_info' => 'La fecha se establece automáticamente a hoy',
    'tooltip_type_info' => 'Seleccione el tipo de donación',
    
    // Resumen de errores
    'error_summary_title' => 'Su formulario contiene errores',
    'error_summary_fix' => 'Por favor, corríjalos antes de continuar',
    'error_summary_count' => 'Se encontró {count} error(es)',
    
    // Accesibilidad
    'aria_required' => 'campo obligatorio',
    'aria_invalid' => 'campo inválido',
    'aria_valid' => 'campo válido',
    
    // Formato
    'currency_symbol' => '€',
    'decimal_separator' => ',',
    'thousands_separator' => '.',
    
    // Formato de fecha
    'date_format' => 'dd/mm/yyyy',
    'today' => 'Hoy',
    
    // Meses para datepicker
    'january' => 'Enero',
    'february' => 'Febrero',
    'march' => 'Marzo',
    'april' => 'Abril',
    'may' => 'Mayo',
    'june' => 'Junio',
    'july' => 'Julio',
    'august' => 'Agosto',
    'september' => 'Septiembre',
    'october' => 'Octubre',
    'november' => 'Noviembre',
    'december' => 'Diciembre',
    
    // Días para datepicker
    'monday' => 'Lunes',
    'tuesday' => 'Martes',
    'wednesday' => 'Miércoles',
    'thursday' => 'Jueves',
    'friday' => 'Viernes',
    'saturday' => 'Sábado',
    'sunday' => 'Domingo',
    
    // Días cortos
    'mon' => 'Lun',
    'tue' => 'Mar',
    'wed' => 'Mié',
    'thu' => 'Jue',
    'fri' => 'Vie',
    'sat' => 'Sáb',
    'sun' => 'Dom',
    
    // Estados de botones
    'button_submit' => 'Enviar',
    'button_submitting' => 'Enviando...',
    'button_reset' => 'Restablecer',
    'button_cancel' => 'Cancelar',
    'button_back' => 'Atrás',
    'button_next' => 'Siguiente',
    'button_confirm' => 'Confirmar',
    
    // Indicadores de progreso
    'progress_step' => 'Paso {current} de {total}',
    'progress_complete' => 'Completado',
    
    // Notificaciones
    'notification_success' => 'Éxito',
    'notification_error' => 'Error',
    'notification_warning' => 'Advertencia',
    'notification_info' => 'Información',
    
    // Secciones del formulario
    'section_donor_info' => 'Información del Donante',
    'section_donation_details' => 'Detalles de la Donación',
    'section_payment' => 'Pago',
    'section_confirmation' => 'Confirmación',
    // stats-live.php específicas
    'site_title' => 'Mind Arena',
    'live_stats' => 'Estadísticas en Vivo',
    'home' => 'Inicio',
    'admin_area' => 'Área de Administración',
    'live_dashboard' => 'Panel en Vivo',
    'last_update' => 'Última actualización',
    'next_update' => 'Próxima actualización',
    'refresh_now' => 'Actualizar ahora',
    'statistics_dashboard' => 'Panel de Estadísticas',
    'dashboard_subtitle' => 'Análisis en tiempo real de donaciones y contribuciones',
    'collected_today' => 'Recolectado hoy',
    'today_donations' => 'Donaciones de hoy',
    'average_per_donation' => 'Promedio por donación',
    'total_general' => 'Total general',
    'detailed_analysis' => 'Análisis Detallado',
    'analysis_subtitle' => 'Ver tendencias y distribuciones',
    'trends' => 'Tendencias',
    'distribution' => 'Distribución',
    'organizations' => 'Organizaciones',
    'recent_donations' => 'Donaciones recientes',
    '7_days_evolution' => 'Evolución 7 Días',
    'last_week' => 'Última semana',
    'realtime_update' => 'Actualización en tiempo real',
    'activity_by_hour' => 'Actividad por hora',
    'today' => 'Hoy',
    'updated_every_30_seconds' => 'Actualizado cada 30 segundos',
    'amount_distribution' => 'Distribución de Montos',
    'all_donations' => 'Todas las donaciones',
    'instant_update' => 'Actualización instantánea',
    'top_organizations' => 'Top Organizaciones',
    'by_amount_collected' => 'Por monto recolectado',
    'live_data' => 'Datos en vivo',
    'performance_by_organization' => 'Rendimiento por organización',
    'performance_subtitle' => 'Comparación de rendimiento de organizaciones',
    'latest_donations' => 'Últimas donaciones',
    'realtime_feed' => 'Flujo en tiempo real',
    'anonymous_donor' => 'Donante anónimo',
    'updated_live' => 'Actualizado en vivo',
    'realtime_dashboard' => 'Panel en tiempo real',
    'auto_update_30_seconds' => 'Auto-actualización cada 30 segundos',
    'current_language' => 'Idioma actual',
    'currency' => 'Moneda',
    'dashboard_updated_success' => '¡Panel actualizado con éxito!',
    'auto_update_in_progress' => 'Auto-actualización en progreso...',
    'manual_update_in_progress' => 'Actualización manual en progreso...',
    
    // Para gráficos
    'total_amount' => 'Monto total',
    'number_of_donations' => 'Número de donaciones',
    'amount' => 'Monto',
    'amount_per_hour' => 'Monto por hora',
    'donations' => 'donaciones',
    'average' => 'Promedio',
    
    // Para estadísticas
    'donations_count' => 'Conteo de donaciones',
    'max_donation' => 'Donación máxima',
    'min_donation' => 'Donación mínima',
    'hourly_activity' => 'Actividad horaria',
    'daily_activity' => 'Actividad diaria',
    'weekly_activity' => 'Actividad semanal',
    'monthly_activity' => 'Actividad mensual',
    
    // Para tablas
    'donor_name' => 'Nombre del donante',
    'donation_date' => 'Fecha de donación',
    'donation_amount' => 'Monto de donación',
    'organization_name' => 'Nombre de la organización',
    
    // Mensajes
    'no_data_available' => 'No hay datos disponibles',
    'loading_data' => 'Cargando datos...',
    'data_loaded_successfully' => 'Datos cargados exitosamente',
    'error_loading_data' => 'Error cargando datos',
    
    // Tooltips
    'click_to_refresh' => 'Click para actualizar',
    'hover_for_details' => 'Pasar para detalles',
    'view_details' => 'Ver detalles',
    
    // Filtros
    'filter_by_date' => 'Filtrar por fecha',
    'filter_by_organization' => 'Filtrar por organización',
    'filter_by_amount' => 'Filtrar por monto',
    'apply_filters' => 'Aplicar filtros',
    'reset_filters' => 'Restablecer filtros',
    
    // Exportación
    'export_data' => 'Exportar datos',
    'export_csv' => 'Exportar como CSV',
    'export_pdf' => 'Exportar como PDF',
    'export_excel' => 'Exportar como Excel',
    
    // Etiquetas de estadísticas
    'stats_overview' => 'Resumen de estadísticas',
    'daily_stats' => 'Estadísticas diarias',
    'weekly_stats' => 'Estadísticas semanales',
    'monthly_stats' => 'Estadísticas mensuales',
    'yearly_stats' => 'Estadísticas anuales',
    
    // Períodos de tiempo
    'last_24_hours' => 'Últimas 24 horas',
    'last_7_days' => 'Últimos 7 días',
    'last_30_days' => 'Últimos 30 días',
    'last_3_months' => 'Últimos 3 meses',
    'last_year' => 'Último año',
    'custom_range' => 'Rango personalizado',
];