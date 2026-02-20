<?php
/**
 * OpticWeb Widget Tools
 * A suite of custom widgets for Creative Elements
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ow_CustomMenu extends Module
{
    public function __construct()
    {
        $this->name = 'ow_custommenu'; // Το τεχνικό όνομα μένει ίδιο για ασφάλεια
        $this->tab = 'front_office_features';
        $this->version = '1.1.0'; // Ανεβάζουμε έκδοση
        $this->author = 'OpticWeb - Vasilis Galanakis';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->is_configurable = 1;

        parent::__construct();

        // ΕΔΩ Η ΑΛΛΑΓΗ ΤΟΥ ΟΝΟΜΑΤΟΣ
        $this->displayName = $this->trans('Opticweb Widget Tools', [], 'Modules.OwCustommenu.Admin');
        $this->description = $this->trans('Suite of tools: Popups, Mega Menus, Sidebars & Toggles.', [], 'Modules.OwCustommenu.Admin');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionCreativeElementsInit')
            && $this->installConfiguration();
    }

    public function uninstall()
    {
        Configuration::deleteByName('OW_BRAND_COLOR');
        Configuration::deleteByName('OW_DROPDOWN_ANIMATION');
        Configuration::deleteByName('OW_SHOW_BRANDING');

        return parent::uninstall();
    }

    protected function installConfiguration()
    {
        return Configuration::updateValue('OW_BRAND_COLOR', '#268CCD')
            && Configuration::updateValue('OW_DROPDOWN_ANIMATION', 'move-up')
            && Configuration::updateValue('OW_SHOW_BRANDING', '1');
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitOwSettings')) {
            $brand_color = Tools::getValue('OW_BRAND_COLOR');
            $dropdown_animation = Tools::getValue('OW_DROPDOWN_ANIMATION');
            $show_branding = (int)Tools::getValue('OW_SHOW_BRANDING');

            // Basic hex color validation
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $brand_color)) {
                $output .= $this->displayError($this->trans('Invalid brand color value.', [], 'Modules.OwCustommenu.Admin'));
            } else {
                Configuration::updateValue('OW_BRAND_COLOR', $brand_color);
                Configuration::updateValue('OW_DROPDOWN_ANIMATION', $dropdown_animation);
                Configuration::updateValue('OW_SHOW_BRANDING', (string)$show_branding);
                $output .= $this->displayConfirmation($this->trans('Settings saved successfully.', [], 'Modules.OwCustommenu.Admin'));
            }
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitOwSettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        ];

        return $output . $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Global Settings', [], 'Modules.OwCustommenu.Admin'),
                    'icon'  => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type'  => 'color',
                        'label' => $this->trans('Brand Color', [], 'Modules.OwCustommenu.Admin'),
                        'name'  => 'OW_BRAND_COLOR',
                        'desc'  => $this->trans('Used as default color for badges, headers and buttons across all widgets.', [], 'Modules.OwCustommenu.Admin'),
                    ],
                    [
                        'type'    => 'select',
                        'label'   => $this->trans('Default Dropdown Animation', [], 'Modules.OwCustommenu.Admin'),
                        'name'    => 'OW_DROPDOWN_ANIMATION',
                        'options' => [
                            'query' => [
                                ['id' => 'none',    'name' => $this->trans('None', [], 'Modules.OwCustommenu.Admin')],
                                ['id' => 'fade',    'name' => $this->trans('Fade In', [], 'Modules.OwCustommenu.Admin')],
                                ['id' => 'move-up', 'name' => $this->trans('Move Up', [], 'Modules.OwCustommenu.Admin')],
                                ['id' => 'zoom',    'name' => $this->trans('Zoom In', [], 'Modules.OwCustommenu.Admin')],
                            ],
                            'id'   => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type'    => 'switch',
                        'label'   => $this->trans('Show Branding Block in Editor', [], 'Modules.OwCustommenu.Admin'),
                        'name'    => 'OW_SHOW_BRANDING',
                        'desc'    => $this->trans('If disabled, the Opticweb info card will not appear in the widget editor panels.', [], 'Modules.OwCustommenu.Admin'),
                        'values'  => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Modules.OwCustommenu.Admin')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Modules.OwCustommenu.Admin')],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.OwCustommenu.Admin'),
                ],
            ],
        ];
    }

    protected function getConfigFieldsValues()
    {
        $show_branding = Configuration::get('OW_SHOW_BRANDING');

        return [
            'OW_BRAND_COLOR'        => Configuration::get('OW_BRAND_COLOR') ?: '#268CCD',
            'OW_DROPDOWN_ANIMATION' => Configuration::get('OW_DROPDOWN_ANIMATION') ?: 'move-up',
            'OW_SHOW_BRANDING'      => (int)($show_branding !== false ? $show_branding : 1),
        ];
    }

    public function hookActionCreativeElementsInit()
    {
        // 1. ΔΗΜΙΟΥΡΓΙΑ ΚΑΤΗΓΟΡΙΑΣ "OPTICWEB" ΣΤΟΝ EDITOR
        \CE\Plugin::instance()->elements_manager->addCategory('opticweb', [
            'title' => 'Opticweb Tools',
            'icon' => 'fa fa-star', // Ένα ωραίο εικονίδιο
        ]);

        // 2. ΦΟΡΤΩΣΗ ΤΩΝ WIDGETS
        $widgets_manager = \CE\Plugin::instance()->widgets_manager;
        $base_dir = _PS_MODULE_DIR_ . $this->name . '/classes/';

        $widgets = [
            'WidgetCustomMenu.php'   => '\OpticWeb\Widgets\WidgetCustomMenu',
            'WidgetToggleMenu.php'   => '\OpticWeb\Widgets\WidgetToggleMenu',
            'WidgetPopUp.php'        => '\OpticWeb\Widgets\WidgetPopUp',
            'WidgetVerticalMenu.php' => '\OpticWeb\Widgets\WidgetVerticalMenu'
        ];

        foreach ($widgets as $file => $class) {
            $file_path = $base_dir . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
                if (class_exists($class)) {
                    $widgets_manager->registerWidgetType(new $class());
                }
            }
        }
    }
}