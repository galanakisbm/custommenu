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

        parent::__construct();

        // ΕΔΩ Η ΑΛΛΑΓΗ ΤΟΥ ΟΝΟΜΑΤΟΣ
        $this->displayName = $this->trans('Opticweb Widget Tools', [], 'Modules.OwCustommenu.Admin');
        $this->description = $this->trans('Suite of tools: Popups, Mega Menus, Sidebars & Toggles.', [], 'Modules.OwCustommenu.Admin');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionCreativeElementsInit');
    }

    public function uninstall()
    {
        return parent::uninstall();
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