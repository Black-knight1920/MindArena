<?php
return [
    // Site geral
    'site_title' => 'GameForGood',
    'home' => 'Início',
    'live_stats' => 'Estatísticas ao Vivo',
    'admin_area' => 'Área do Administrador',
    'copyright' => '© 2024 GameForGood - Todos os direitos reservados.',
    'tagline' => 'Jogue por uma boa causa',
    
    // Seção hero
    'game_for_good' => 'GameForGood',
    'welcome' => 'Bem-vindo à nossa plataforma de doações dedicada a organizações beneficentes',
    'slogan' => 'Seu jogo pode mudar o mundo',
    'support_cause' => 'Apoiar uma causa',
    'see_live_stats' => 'Ver estatísticas ao vivo',
    
    // Organizações
    'our_partners' => 'Nossos Parceiros',
    'choose_cause' => 'Escolha uma causa que você se importa e doe agora',
    'progress' => 'Progresso',
    'collected' => 'arrecadado',
    'goal' => 'Meta',
    'remaining' => 'Restante',
    'to_collect' => 'para arrecadar',
    'goal_reached' => 'Meta alcançada!',
    'make_donation' => 'Fazer uma doação',
    'visit_website' => 'Visitar site',
    
    // Doações
    'donations' => 'Doações',
    'donors' => 'Doadores',
    'last_donation' => 'Última doação',
    'donation_success' => 'Obrigado pela sua doação!',
    
    // Idioma e Moeda
    'current_language' => 'Idioma atual',
    'currency' => 'Moeda',
    'currency_symbol' => 'R$',
    'currency_code' => 'BRL',
    'decimal_separator' => ',',
    'thousands_separator' => '.',
    
    // Rótulos do formulário
    'name' => 'Nome',
    'email' => 'Email',
    'amount' => 'Valor',
    'message' => 'Mensagem (opcional)',
    'submit' => 'Enviar',
    'select_organisation' => 'Selecionar organização',
    
    // Mensagens
    'required_field' => 'Este campo é obrigatório',
    'invalid_email' => 'Email inválido',
    'min_amount' => 'O valor mínimo é {amount}',
    'thank_you' => 'Obrigado pela sua generosidade',
    'donation_recorded' => 'Sua doação foi registrada com sucesso',
    
    // Formas plurais
    'donor_one' => '{count} doador',
    'donor_many' => '{count} doadores',
    'donation_one' => '{count} doação',
    'donation_many' => '{count} doações',
    'real_one' => '{count} real',
    'real_many' => '{count} reais',
    
    // Estatísticas
    'total_donations' => 'Total de doações',
    'total_donors' => 'Total de doadores',
    'average_donation' => 'Doação média',
    'top_donor' => 'Maior doador',
    'recent_donations' => 'Doações recentes',
    'live_updates' => 'Atualizações ao vivo',
    
    // Tempo
    'just_now' => 'Agora mesmo',
    'minutes_ago' => 'Há {count} minuto(s)',
    'hours_ago' => 'Há {count} hora(s)',
    'days_ago' => 'Há {count} dia(s)',
    
    // Meses (para formatDate)
    'months' => ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 
                'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'],
    
    // Dias (para formatDate)
    'days' => ['segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 
              'sexta-feira', 'sábado', 'domingo'],
    
    // Formulário de doação específico
    'make_donation' => 'Faça uma Doação',
    'make_difference' => 'Sua doação faz a diferença',
    'error_no_org_selected' => 'Nenhuma organização selecionada. Por favor, escolha uma organização na página inicial.',
    'donor_lastname' => 'Sobrenome do doador',
    'placeholder_lastname' => 'Seu sobrenome',
    'donor_firstname' => 'Nome do doador',
    'placeholder_firstname' => 'Seu nome',
    'donor_email' => 'Email do doador',
    'optional' => 'opcional',
    'donation_amount' => 'Valor da doação',
    'custom_amount' => 'Outro valor',
    'donation_date' => 'Data da doação',
    'date_auto_today' => 'Data automática (hoje)',
    'donation_type' => 'Tipo de doação',
    'select_type' => 'Selecionar tipo',
    'monetary' => 'Monetária',
    'material' => 'Material',
    'back_home' => 'Voltar ao Início',
    'complete_donation' => 'Completar doação',
    'secure_payment' => 'Pagamento seguro',
    'data_protected' => 'Dados protegidos',
    'back_to_home' => 'Voltar ao Início',
    'back_to_home_to_select' => 'Voltar ao Início para escolher',
    'processing' => 'Processando',
    
    // Mensagens de erro
    'error_amount_zero' => 'O valor deve ser maior que zero',
    'error_invalid_date' => 'Data inválida',
    'error_date_today' => 'A data deve ser a de hoje',
    'error_donation_type' => 'Por favor, selecione um tipo de doação',
    'error_select_org' => 'Por favor, selecione uma organização',
    'error_donor_name' => 'O sobrenome é obrigatório',
    'error_invalid_name' => 'Sobrenome inválido (caracteres especiais não permitidos)',
    'error_name_min_length' => 'O sobrenome deve ter pelo menos 2 caracteres',
    'error_name_max_length' => 'O sobrenome não pode exceder 50 caracteres',
    'error_donor_firstname' => 'O nome é obrigatório',
    'error_invalid_firstname' => 'Nome inválido (caracteres especiais não permitidos)',
    'error_firstname_min_length' => 'O nome deve ter pelo menos 2 caracteres',
    'error_firstname_max_length' => 'O nome não pode exceder 50 caracteres',
    'error_invalid_email' => 'Endereço de email inválido',
    'donation_error' => 'Ocorreu um erro ao registrar a doação',
    'validation_errors' => 'Erros de validação',
    'please_correct_errors' => 'Por favor, corrija os erros abaixo',
    'validation_please_correct' => 'Por favor, corrija os seguintes erros:',
    'error_occurred' => 'Erro',
    // Mensagens de Validação JavaScript
    'validation_required' => 'Este campo é obrigatório',
    'validation_min_length' => 'Deve ter pelo menos {min} caracteres',
    'validation_max_length' => 'Não pode exceder {max} caracteres',
    'validation_invalid_email' => 'Endereço de email inválido',
    'validation_invalid_name' => 'Apenas letras, espaços, hífens e apóstrofos são permitidos',
    'validation_invalid_amount' => 'Valor inválido',
    'validation_amount_min' => 'O valor mínimo é {min}',
    'validation_amount_max' => 'O valor máximo é {max}',
    'validation_required_selection' => 'Por favor, faça uma seleção',
    'validation_processing' => 'Processando...',
    
    // Mensagens específicas do campo
    'validation_lastname_required' => 'O sobrenome é obrigatório',
    'validation_firstname_required' => 'O nome é obrigatório',
    'validation_amount_required' => 'O valor é obrigatório',
    'validation_type_required' => 'O tipo de doação é obrigatório',
    'validation_date_required' => 'A data é obrigatória',
    
    // Mensagens de sucesso
    'validation_field_valid' => 'Campo válido',
    'validation_form_valid' => 'Formulário válido',
    
    // Contadores de caracteres
    'characters' => 'caracteres',
    'characters_remaining' => 'caracteres restantes',
    
    // Botões de valor
    'amount_10' => '10 R$',
    'amount_25' => '25 R$',
    'amount_50' => '50 R$',
    'amount_100' => '100 R$',
    'amount_250' => '250 R$',
    'amount_500' => '500 R$',
    
    // Atualizações de placeholder
    'placeholder_select_type' => 'Selecionar...',
    
    // Mensagens de confirmação
    'confirm_submission' => 'Confirmar envio',
    'confirm_submit_donation' => 'Tem certeza de que deseja enviar esta doação?',
    'confirm_yes' => 'Sim, continuar',
    'confirm_no' => 'Não, cancelar',
    
    // Estados de carregamento
    'loading' => 'Carregando...',
    'saving' => 'Salvando...',
    'redirecting' => 'Redirecionando...',
    
    // Mensagens do navegador
    'browser_validation_email' => 'Por favor, insira um endereço de email válido',
    'browser_validation_required' => 'Por favor, preencha este campo',
    'browser_validation_pattern' => 'Formato incorreto',
    
    // Tooltips
    'tooltip_name_format' => 'Apenas letras, espaços, hífens (-) e apóstrofos (\')',
    'tooltip_email_optional' => 'Opcional - para o recibo',
    'tooltip_amount_quick' => 'Clique para selecionar',
    'tooltip_amount_custom' => 'Digite valor personalizado',
    'tooltip_date_info' => 'A data é definida automaticamente para hoje',
    'tooltip_type_info' => 'Selecione o tipo de doação',
    
    // Resumo de erros
    'error_summary_title' => 'Seu formulário contém erros',
    'error_summary_fix' => 'Por favor, corrija-os antes de continuar',
    'error_summary_count' => 'Encontrado {count} erro(s)',
    
    // Acessibilidade
    'aria_required' => 'campo obrigatório',
    'aria_invalid' => 'campo inválido',
    'aria_valid' => 'campo válido',
    
    // Formatação
    'currency_symbol' => 'R$',
    'decimal_separator' => ',',
    'thousands_separator' => '.',
    
    // Formato de data
    'date_format' => 'dd/mm/yyyy',
    'today' => 'Hoje',
    
    // Meses para datepicker
    'january' => 'Janeiro',
    'february' => 'Fevereiro',
    'march' => 'Março',
    'april' => 'Abril',
    'may' => 'Maio',
    'june' => 'Junho',
    'july' => 'Julho',
    'august' => 'Agosto',
    'september' => 'Setembro',
    'october' => 'Outubro',
    'november' => 'Novembro',
    'december' => 'Dezembro',
    
    // Dias para datepicker
    'monday' => 'Segunda-feira',
    'tuesday' => 'Terça-feira',
    'wednesday' => 'Quarta-feira',
    'thursday' => 'Quinta-feira',
    'friday' => 'Sexta-feira',
    'saturday' => 'Sábado',
    'sunday' => 'Domingo',
    
    // Dias curtos
    'mon' => 'Seg',
    'tue' => 'Ter',
    'wed' => 'Qua',
    'thu' => 'Qui',
    'fri' => 'Sex',
    'sat' => 'Sáb',
    'sun' => 'Dom',
    
    // Estados dos botões
    'button_submit' => 'Enviar',
    'button_submitting' => 'Enviando...',
    'button_reset' => 'Redefinir',
    'button_cancel' => 'Cancelar',
    'button_back' => 'Voltar',
    'button_next' => 'Próximo',
    'button_confirm' => 'Confirmar',
    
    // Indicadores de progresso
    'progress_step' => 'Passo {current} de {total}',
    'progress_complete' => 'Concluído',
    
    // Notificações
    'notification_success' => 'Sucesso',
    'notification_error' => 'Erro',
    'notification_warning' => 'Aviso',
    'notification_info' => 'Informação',
    
    // Seções do formulário
    'section_donor_info' => 'Informações do Doador',
    'section_donation_details' => 'Detalhes da Doação',
    'section_payment' => 'Pagamento',
    'section_confirmation' => 'Confirmação',
    // stats-live.php específicas
    'site_title' => 'Mind Arena',
    'live_stats' => 'Estatísticas ao Vivo',
    'home' => 'Início',
    'admin_area' => 'Área Administrativa',
    'live_dashboard' => 'Painel em Tempo Real',
    'last_update' => 'Última atualização',
    'next_update' => 'Próxima atualização',
    'refresh_now' => 'Atualizar agora',
    'statistics_dashboard' => 'Painel de Estatísticas',
    'dashboard_subtitle' => 'Análise em tempo real de doações e contribuições',
    'collected_today' => 'Arrecadado hoje',
    'today_donations' => 'Doações de hoje',
    'average_per_donation' => 'Média por doação',
    'total_general' => 'Total geral',
    'detailed_analysis' => 'Análise Detalhada',
    'analysis_subtitle' => 'Visualizar tendências e distribuições',
    'trends' => 'Tendências',
    'distribution' => 'Distribuição',
    'organizations' => 'Organizações',
    'recent_donations' => 'Doações recentes',
    '7_days_evolution' => 'Evolução 7 Dias',
    'last_week' => 'Última semana',
    'realtime_update' => 'Atualização em tempo real',
    'activity_by_hour' => 'Atividade por hora',
    'today' => 'Hoje',
    'updated_every_30_seconds' => 'Atualizado a cada 30 segundos',
    'amount_distribution' => 'Distribuição de Valores',
    'all_donations' => 'Todas as doações',
    'instant_update' => 'Atualização instantânea',
    'top_organizations' => 'Top Organizações',
    'by_amount_collected' => 'Por valor arrecadado',
    'live_data' => 'Dados ao vivo',
    'performance_by_organization' => 'Desempenho por organização',
    'performance_subtitle' => 'Comparação de desempenho das organizações',
    'latest_donations' => 'Últimas doações',
    'realtime_feed' => 'Feed em tempo real',
    'anonymous_donor' => 'Doador anônimo',
    'updated_live' => 'Atualizado ao vivo',
    'realtime_dashboard' => 'Painel em tempo real',
    'auto_update_30_seconds' => 'Auto-atualização a cada 30 segundos',
    'current_language' => 'Idioma atual',
    'currency' => 'Moeda',
    'dashboard_updated_success' => 'Painel atualizado com sucesso!',
    'auto_update_in_progress' => 'Auto-atualização em andamento...',
    'manual_update_in_progress' => 'Atualização manual em andamento...',
    
    // Para gráficos
    'total_amount' => 'Valor total',
    'number_of_donations' => 'Número de doações',
    'amount' => 'Valor',
    'amount_per_hour' => 'Valor por hora',
    'donations' => 'doações',
    'average' => 'Média',
    
    // Para estatísticas
    'donations_count' => 'Contagem de doações',
    'max_donation' => 'Doação máxima',
    'min_donation' => 'Doação mínima',
    'hourly_activity' => 'Atividade horária',
    'daily_activity' => 'Atividade diária',
    'weekly_activity' => 'Atividade semanal',
    'monthly_activity' => 'Atividade mensal',
    
    // Para tabelas
    'donor_name' => 'Nome do doador',
    'donation_date' => 'Data da doação',
    'donation_amount' => 'Valor da doação',
    'organization_name' => 'Nome da organização',
    
    // Mensagens
    'no_data_available' => 'Nenhum dado disponível',
    'loading_data' => 'Carregando dados...',
    'data_loaded_successfully' => 'Dados carregados com sucesso',
    'error_loading_data' => 'Erro ao carregar dados',
    
    // Tooltips
    'click_to_refresh' => 'Clique para atualizar',
    'hover_for_details' => 'Passe o mouse para detalhes',
    'view_details' => 'Ver detalhes',
    
    // Filtros
    'filter_by_date' => 'Filtrar por data',
    'filter_by_organization' => 'Filtrar por organização',
    'filter_by_amount' => 'Filtrar por valor',
    'apply_filters' => 'Aplicar filtros',
    'reset_filters' => 'Redefinir filtros',
    
    // Exportação
    'export_data' => 'Exportar dados',
    'export_csv' => 'Exportar como CSV',
    'export_pdf' => 'Exportar como PDF',
    'export_excel' => 'Exportar como Excel',
    
    // Rótulos de estatísticas
    'stats_overview' => 'Visão geral das estatísticas',
    'daily_stats' => 'Estatísticas diárias',
    'weekly_stats' => 'Estatísticas semanais',
    'monthly_stats' => 'Estatísticas mensais',
    'yearly_stats' => 'Estatísticas anuais',
    
    // Períodos de tempo
    'last_24_hours' => 'Últimas 24 horas',
    'last_7_days' => 'Últimos 7 dias',
    'last_30_days' => 'Últimos 30 dias',
    'last_3_months' => 'Últimos 3 meses',
    'last_year' => 'Último ano',
    'custom_range' => 'Intervalo personalizado',
];