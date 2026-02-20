<?php
namespace OpticWeb\Widgets;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WidgetVerticalMenu extends \CE\WidgetBase
{
    public function getName() { return 'ow_vertical_menu'; }
    public function get_name() { return 'ow_vertical_menu'; }
    public function getTitle() { return 'Opticweb Vertical Menu'; }
    public function getIcon() { return 'eicon-nav-menu'; }
    public function getCategories() { return ['opticweb']; }

    protected function get_ps_categories()
    {
        $id_lang = (int)\Context::getContext()->language->id;
        $categories = \Category::getCategories($id_lang, true, false);
        $options = ['0' => 'Επιλέξτε Κατηγορία...'];
        foreach ($categories as $category) {
            $options[$category['id_category']] = $category['name'];
        }
        return $options;
    }

    protected function registerControls()
    {
        $brand_color = \Configuration::get('OW_BRAND_COLOR') ?: '#268CCD';
        $show_branding = \Configuration::get('OW_SHOW_BRANDING');
        if ($show_branding === false) {
            $show_branding = '1';
        }

        // --- 1. HEADER SETTINGS ---
        $this->startControlsSection('section_header_settings', ['label' => 'Ρυθμίσεις Επικεφαλίδας']);
        $this->addControl('header_mode', [
            'label' => 'Λειτουργία', 'type' => 'select', 'default' => 'static',
            'options' => ['static' => 'Static (Πάντα Ανοιχτό)', 'button' => 'Button (Άνοιγμα/Κλείσιμο)'],
        ]);
        $this->addControl('menu_title', ['label' => 'Τίτλος', 'type' => 'text', 'default' => 'ΚΑΤΗΓΟΡΙΕΣ']);
        $this->addControl('header_icon', [
            'label' => 'Εικονίδιο',
            'type' => 'icons', 
            'default' => ['value' => 'fas fa-bars', 'library' => 'fa-solid'],
        ]);
        $this->addControl('start_closed', ['label' => 'Αρχικά Κλειστό;', 'type' => 'switcher', 'default' => 'yes', 'condition' => ['header_mode' => 'button']]);
        $this->endControlsSection();

        // --- 2. CONTENT (STRUCTURE) ---
        $this->startControlsSection('section_content', ['label' => 'Δομή Μενού']);
        $repeater = new \CE\Repeater();
        $repeater->addControl('item_type', [
            'label' => 'Τύπος', 'type' => 'select', 'default' => 'main',
            'options' => [
                'main' => '1. Κεντρικό (Level 1)', 
                'column' => '2. Στήλη (Level 2)', 
                'html' => '2. Custom HTML (Editor)', 
                'link' => '3. Σύνδεσμος (Level 3)'
            ],
        ]);
        
        $repeater->addControl('is_linkable', [
            'label' => 'Ενεργό Link (L1)',
            'type' => 'switcher',
            'label_on' => 'Ναι',
            'label_off' => 'Όχι',
            'default' => 'yes',
            'condition' => ['item_type' => 'main'],
            'description' => 'Αν επιλέξετε "Όχι", ο τίτλος δεν θα είναι κλικαμπλ σύνδεσμος.',
        ]);

        $repeater->addControl('item_text', ['label' => 'Κείμενο', 'type' => 'text', 'condition' => ['item_type!' => 'html']]);
        $repeater->addControl('item_icon', [
            'label' => 'Εικονίδιο / SVG',
            'type' => 'icons',
            'default' => ['value' => '', 'library' => ''],
            'condition' => ['item_type!' => 'html']
        ]);

        $repeater->addControl('item_html', [
            'label' => 'Περιεχόμενο HTML', 
            'type' => \CE\ControlsManager::WYSIWYG, 
            'condition' => ['item_type' => 'html']
        ]);

        $repeater->addControl('link_type', ['label' => 'Πηγή Link', 'type' => 'select', 'default' => 'custom', 'options' => ['custom' => 'Manual', 'category' => 'Category']]);
        $repeater->addControl('category_id', ['label' => 'Κατηγορία', 'type' => 'select', 'options' => $this->get_ps_categories(), 'condition' => ['link_type' => 'category']]);
        $repeater->addControl('auto_sub', ['label' => 'Auto Subs (2 Levels)', 'type' => 'switcher', 'condition' => ['link_type' => 'category', 'item_type' => 'main']]);
        
        $repeater->addControl('is_mega', ['label' => 'Mega Panel', 'type' => 'switcher', 'condition' => ['item_type' => 'main']]);
        
        $repeater->addControl('dropdown_width', [
            'label' => 'Πλάτος Panel', 
            'type' => 'slider', 
            'size_units' => ['px', '%'],
            'range' => ['px' => ['min' => 200, 'max' => 1200], '%' => ['min' => 10, 'max' => 100]], 
            'default' => ['unit' => 'px', 'size' => 600], 
            'condition' => ['item_type' => 'main']
        ]);
        
        $repeater->addControl('columns_num', [
            'label' => 'Στήλες Grid', 'type' => 'select', 'default' => '3', 
            'options' => ['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6'], 
            'condition' => ['item_type' => 'main']
        ]);
        $repeater->addControl('item_link', ['label' => 'URL', 'type' => 'url', 'condition' => ['link_type' => 'custom']]);
        $repeater->addControl('badge_text', ['label' => 'Badge Text', 'type' => 'text', 'separator' => 'before']);
        $repeater->addControl('badge_bg', ['label' => 'Badge BG', 'type' => 'color', 'default' => $brand_color, 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} .ow-badge' => 'background-color: {{VALUE}};']]);
        $repeater->addControl('badge_color', ['label' => 'Badge Color', 'type' => 'color', 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} .ow-badge' => 'color: {{VALUE}};']]);

        $this->addControl('menu_items', ['label' => 'Στοιχεία Μενού', 'type' => 'repeater', 'fields' => $repeater->getControls(), 'titleField' => '{{{ item_text }}}']);
        $this->endControlsSection();

        // --- 3. STYLE: HEADER ---
        $this->startControlsSection('style_header', ['label' => 'Style: Επικεφαλίδα / Κουμπί', 'tab' => 'style']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'header_typo', 'selector' => '{{WRAPPER}} .ow-vm-header']);
        $this->startControlsTabs('header_tabs');
        $this->startControlsTab('header_n', ['label' => 'Normal']);
        $this->addControl('header_bg', ['label' => 'Background', 'type' => 'color', 'default' => $brand_color, 'selectors' => ['{{WRAPPER}} .ow-vm-header' => 'background-color: {{VALUE}};']]);
        $this->addControl('header_color', ['label' => 'Text Color', 'type' => 'color', 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .ow-vm-header' => 'color: {{VALUE}};']]);
        $this->endControlsTab();
        $this->startControlsTab('header_h', ['label' => 'Hover']);
        $this->addControl('header_bg_h', ['label' => 'Background', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-vm-header:hover' => 'background-color: {{VALUE}};']]);
        $this->addControl('header_color_h', ['label' => 'Text Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-vm-header:hover' => 'color: {{VALUE}};']]);
        $this->endControlsTab();
        $this->endControlsTabs();
        $this->addControl('header_rad', ['label' => 'Border Radius', 'type' => 'dimensions', 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .ow-vm-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->addControl('header_pad', ['label' => 'Padding', 'type' => 'dimensions', 'selectors' => ['{{WRAPPER}} .ow-vm-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->addControl('header_min_height', [
            'label' => 'Min Height (px)', 'type' => 'slider', 'range' => ['px' => ['min' => 20, 'max' => 200]],
            'selectors' => ['{{WRAPPER}} .ow-vm-header' => 'min-height: {{SIZE}}{{UNIT}};'],
        ]);
        $this->addControl('header_icon_size', ['label' => 'Icon Size', 'type' => 'slider', 'range' => ['px' => ['min' => 10, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .ow-vm-header i, {{WRAPPER}} .ow-vm-header svg' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};']]);
        $this->endControlsSection();

        // --- 4. STYLE: ITEMS (Level 1) ---
        $this->startControlsSection('style_items', ['label' => 'Style: Λίστα (Level 1)', 'tab' => 'style']);
        $this->addControl('l1_width', [
            'label' => 'Width (L1)', 'type' => 'slider', 'size_units' => ['px', '%'],
            'range' => ['px' => ['min' => 100, 'max' => 1000], '%' => ['min' => 10, 'max' => 100]],
            'default' => ['unit' => '%', 'size' => 100], 'selectors' => ['{{WRAPPER}} .ow-vm-ul' => 'width: {{SIZE}}{{UNIT}};'],
        ]);
        $this->addControl('l1_h_offset', [
            'label' => 'Horizontal Offset (L1)', 'type' => 'slider', 'size_units' => ['px', '%'],
            'range' => ['px' => ['min' => -200, 'max' => 200], '%' => ['min' => -50, 'max' => 50]],
            'selectors' => ['{{WRAPPER}} .ow-vm-ul' => 'left: {{SIZE}}{{UNIT}};'],
        ]);
        $this->addControl('l1_v_offset', [
            'label' => 'Vertical Offset (L1)', 'type' => 'slider', 'size_units' => ['px'],
            'range' => ['px' => ['min' => -50, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .ow-vm-ul' => 'margin-top: {{SIZE}}{{UNIT}};'],
        ]);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'item_typo', 'selector' => '{{WRAPPER}} .ow-vm-a']);
        $this->addControl('item_bg', ['label' => 'Background', 'type' => 'color', 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .ow-vm-ul' => 'background-color: {{VALUE}};']]);
        $this->startControlsTabs('item_tabs');
        $this->startControlsTab('item_tab_n', ['label' => 'Normal']);
        $this->addControl('link_c', ['label' => 'Text Color', 'type' => 'color', 'default' => '#333', 'selectors' => ['{{WRAPPER}} .ow-vm-a' => 'color: {{VALUE}};']]);
        $this->addControl('link_icon_c', ['label' => 'Icon Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-vm-text i' => 'color: {{VALUE}};', '{{WRAPPER}} .ow-vm-text svg' => 'fill: {{VALUE}};']]);
        $this->endControlsTab();
        $this->startControlsTab('item_tab_h', ['label' => 'Hover']);
        $this->addControl('link_ch', ['label' => 'Text Color', 'type' => 'color', 'default' => $brand_color, 'selectors' => ['{{WRAPPER}} .ow-vm-li:hover > .ow-vm-a' => 'color: {{VALUE}};']]);
        $this->addControl('link_bgh', ['label' => 'Background', 'type' => 'color', 'default' => '#f9f9f9', 'selectors' => ['{{WRAPPER}} .ow-vm-li:hover > .ow-vm-a' => 'background-color: {{VALUE}};']]);
        $this->endControlsTab();
        $this->endControlsTabs();
        $this->addControl('item_border', ['label' => 'Separator Color', 'type' => 'color', 'default' => '#eee', 'selectors' => ['{{WRAPPER}} .ow-vm-li:not(:last-child)' => 'border-bottom: 1px solid {{VALUE}};']]);
        $this->endControlsSection();

        // --- 5. STYLE: DROPDOWN & OFFSET ---
        $this->startControlsSection('style_drop', ['label' => 'Style: Dropdown & Levels', 'tab' => 'style']);
        $this->addControl('drop_bg', ['label' => 'Dropdown BG', 'type' => 'color', 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .ow-vm-dropdown' => 'background-color: {{VALUE}};']]);
        $this->addGroupControl(\CE\GroupControlBoxShadow::getType(), ['name' => 'drop_shadow', 'selector' => '{{WRAPPER}} .ow-vm-dropdown']);
        $this->addControl('drop_h_offset', [
            'label' => 'Horizontal Offset (Gap)', 'type' => 'slider', 'size_units' => ['px'],
            'range' => ['px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .ow-vm-dropdown' => 'margin-left: {{SIZE}}{{UNIT}};'],
        ]);
        $this->addControl('drop_v_offset', [
            'label' => 'Vertical Offset', 'type' => 'slider', 'size_units' => ['px'],
            'range' => ['px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .ow-vm-dropdown' => 'top: {{SIZE}}{{UNIT}};'],
        ]);
        $this->addControl('h_l2', ['label' => 'Level 2 (Titles)', 'type' => 'heading', 'separator' => 'before']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l2_typo', 'selector' => '{{WRAPPER}} .ow-col-title']);
        $this->addControl('l2_c', ['label' => 'Color', 'type' => 'color', 'default' => '#000', 'selectors' => ['{{WRAPPER}} .ow-col-title' => 'color: {{VALUE}} !important;']]);
        $this->addControl('h_l3', ['label' => 'Level 3 (Links)', 'type' => 'heading', 'separator' => 'before']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l3_typo', 'selector' => '{{WRAPPER}} .ow-sub-link']);
        $this->addControl('l3_c', ['label' => 'Color', 'type' => 'color', 'default' => '#666', 'selectors' => ['{{WRAPPER}} .ow-sub-link' => 'color: {{VALUE}};']]);
        $this->endControlsSection();
        
        // --- 6. BRANDING ---
        if ($show_branding !== '0') {
            $this->startControlsSection('section_branding', ['label' => 'Opticweb Support']);
            $logo_path = __PS_BASE_URI__ . 'modules/ow_custommenu/logo.png';
            $this->addControl('branding_html', ['type' => 'raw_html', 'raw' => '<div style="background: '.$brand_color.'; padding: 15px; border-radius: 8px; color: #fff; text-align: center;"><img src="'.$logo_path.'" style="max-width: 120px; margin-bottom: 10px;"><p style="margin:0; font-weight: bold;">Βασίλης Γαλανάκης</p><a href="https://opticweb.gr" target="_blank" style="color: #fff; text-decoration: underline;">www.opticweb.gr</a></div>']);
            $this->endControlsSection();
        }
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        $id_w = $this->getId();
        if (empty($settings['menu_items'])) return;

        $is_button = ($settings['header_mode'] === 'button');
        $start_closed = ($is_button && $settings['start_closed'] === 'yes');
        
        $id_lang = (int)\Context::getContext()->language->id;
        $id_shop = (int)\Context::getContext()->shop->id;
        $link_service = \Context::getContext()->link;
        
        $menu_tree = []; 
        $m_idx = -1;
        $c_idx = -1;

        foreach ($settings['menu_items'] as $item) {
            $item['url'] = ($item['link_type'] === 'category' && (int)$item['category_id'] > 0) ? $link_service->getCategoryLink((int)$item['category_id']) : ($item['item_link']['url'] ?? '#');
            
            if ($item['item_type'] === 'main') {
                $item['cols'] = [];
                if ($item['link_type'] === 'category' && $item['auto_sub'] === 'yes') {
                    $subs = \Category::getChildren((int)$item['category_id'], $id_lang, true, $id_shop);
                    foreach ($subs as $sub) {
                        $new_col = ['item_type' => 'column', 'item_text' => $sub['name'], 'url' => $link_service->getCategoryLink((int)$sub['id_category']), 'links' => []];
                        $grand = \Category::getChildren((int)$sub['id_category'], $id_lang, true, $id_shop);
                        foreach ($grand as $gc) {
                            $gc_item = ['item_text' => $gc['name'], 'url' => $link_service->getCategoryLink((int)$gc['id_category']), 'subs' => []];
                            $great = \Category::getChildren((int)$gc['id_category'], $id_lang, true, $id_shop);
                            foreach ($great as $ggc) { $gc_item['subs'][] = ['item_text' => $ggc['name'], 'url' => $link_service->getCategoryLink((int)$ggc['id_category'])]; }
                            $new_col['links'][] = $gc_item;
                        }
                        $item['cols'][] = $new_col;
                    }
                }
                $menu_tree[] = $item; 
                $m_idx++;
                $c_idx = -1; 
            } 
            else if ($m_idx >= 0) {
                if ($item['item_type'] === 'column' || $item['item_type'] === 'html') {
                    $item['links'] = [];
                    $menu_tree[$m_idx]['cols'][] = $item;
                    $c_idx = count($menu_tree[$m_idx]['cols']) - 1;
                } 
                else if ($item['item_type'] === 'link') {
                    if ($c_idx === -1) {
                        $menu_tree[$m_idx]['cols'][] = ['item_type' => 'column', 'item_text' => '', 'url' => '#', 'links' => []];
                        $c_idx = count($menu_tree[$m_idx]['cols']) - 1;
                    }
                    $menu_tree[$m_idx]['cols'][$c_idx]['links'][] = $item;
                }
            }
        }

        echo '<style>
            .ow-vm-wrapper { width: 100%; position: relative; font-family: inherit; z-index: 99; }
            .ow-vm-header { padding: 15px 20px; font-weight: bold; text-transform: uppercase; display: flex; align-items: center; justify-content: space-between; gap: 10px; transition: 0.3s; height: auto !important; '.($is_button ? 'cursor: pointer;' : 'cursor: default;').' }
            .ow-vm-header i, .ow-vm-header svg { display: inline-block; vertical-align: middle; }
            .ow-vm-ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; position: absolute; top: 100%; left: 0; z-index: 9999; box-shadow: 0 10px 20px rgba(0,0,0,0.15); transition: max-height 0.4s ease-out, opacity 0.4s; overflow: visible; }
            .ow-vm-ul.hidden { display: none; }
            .ow-vm-li { position: relative; width: 100%; transition: all 0.2s ease; overflow: visible; }
            .ow-vm-li::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; transform: scaleY(0); transition: transform 0.3s ease; transform-origin: bottom; z-index: 10; }
            .ow-vm-li:hover::before { transform: scaleY(1); transform-origin: top; }
            
            /* Link Logic Styles */
            .ow-vm-a { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; text-decoration: none !important; transition: 0.2s; width: 100%; box-sizing: border-box; position: relative; z-index: 5; }
            .ow-vm-a.no-link { cursor: default; }
            
            .ow-vm-text { display: flex; align-items: center; gap: 10px; }
            .ow-vm-text svg { width: 1.2em; height: 1.2em; fill: currentColor; }
            .ow-badge { font-size: 9px; font-weight: bold; padding: 2px 5px; border-radius: 3px; margin-left: 5px; white-space: nowrap; }
            .ow-vm-dropdown { display: none; position: absolute; left: 100%; top: 0; min-height: 100%; z-index: 10000; padding: 20px; box-sizing: border-box; opacity: 0; transform: translateX(10px); transition: opacity 0.3s, transform 0.3s; box-shadow: 5px 0 15px rgba(0,0,0,0.15); }
            .ow-vm-li:hover > .ow-vm-dropdown { display: block; opacity: 1; transform: translateX(0); }
            .ow-grid { display: grid; gap: 25px; }
            .ow-col-title { display: block; margin-bottom: 10px; padding-bottom: 5px; text-decoration: none; border-bottom: 1px solid #eee; }
            .ow-sub-link { display: block; padding: 4px 0; text-decoration: none; transition: all 0.3s ease; }
            .ow-sub-link:hover { transform: translateX(5px); }
            .ow-nested-link { padding-left: 10px; display: block; opacity: 0.9; }
            .ow-nested-link::before { content: "\2014"; margin-right: 5px; }
        </style>';

        echo '<div class="ow-vm-wrapper" id="ow-vm-'.$id_w.'">';
        $onclick = $is_button ? 'onclick="document.getElementById(\'ow-list-'.$id_w.'\').classList.toggle(\'hidden\'); this.classList.toggle(\'active\');"' : '';
        echo '<div class="ow-vm-header" '.$onclick.'>';
        echo '<div style="display:flex;align-items:center;gap:10px;">';
        if (!empty($settings['header_icon']['value'])) { \CE\IconsManager::renderIcon($settings['header_icon'], ['aria-hidden' => 'true']); }
        echo '<span>'.htmlspecialchars($settings['menu_title']).'</span>';
        echo '</div>';
        // Only show toggle chevron when in button mode
        if ($is_button) echo '<i class="ceicon-chevron-down ow-vm-toggle-icon"></i>';
        echo '</div>';

        $list_class = 'ow-vm-ul';
        if ($start_closed) $list_class .= ' hidden';
        echo '<ul class="'.$list_class.'" id="ow-list-'.$id_w.'">';
        foreach ($menu_tree as $m) {
            $has_children = !empty($m['cols']);
            $width = $m['dropdown_width']['size'] . $m['dropdown_width']['unit'];
            
            // Link Logic
            $is_linkable = isset($m['is_linkable']) ? $m['is_linkable'] : 'yes';
            $href = ($is_linkable === 'yes') ? 'href="'.htmlspecialchars($m['url']).'"' : 'href="javascript:void(0);"';
            $link_class = ($is_linkable === 'no') ? 'ow-vm-a no-link' : 'ow-vm-a';

            echo '<li class="ow-vm-li elementor-repeater-item-'.$m['_id'].'">';
            echo '<a '.$href.' class="'.$link_class.'">';
            echo '<span class="ow-vm-text">';
            if (!empty($m['item_icon']['value'])) { \CE\IconsManager::renderIcon($m['item_icon'], ['aria-hidden' => 'true']); }
            echo htmlspecialchars($m['item_text']);
            if (!empty($m['badge_text'])) echo '<span class="ow-badge">'.htmlspecialchars($m['badge_text']).'</span>';
            echo '</span>';
            if ($has_children) echo '<i class="ceicon-chevron-right ow-vm-arrow"></i>';
            echo '</a>';
            if ($has_children) {
                echo '<div class="ow-vm-dropdown" style="width: '.$width.';">';
                echo '<div class="ow-grid" style="grid-template-columns: repeat('.$m['columns_num'].', 1fr);">';
                foreach ($m['cols'] as $col) {
                    echo '<div class="ow-col">';
                    if ($col['item_type'] === 'html') { 
                        echo '<div class="ow-html">'.$col['item_html'].'</div>'; 
                    }
                    else {
                        if (!empty($col['item_text'])) {
                            echo '<a href="'.htmlspecialchars($col['url']).'" class="ow-col-title">'.htmlspecialchars($col['item_text']).'</a>';
                        }
                        foreach ($col['links'] as $l) {
                            $l_url = isset($l['url']) ? $l['url'] : ($l['item_link']['url'] ?? '#');
                            echo '<a href="'.htmlspecialchars($l_url).'" class="ow-sub-link">'.htmlspecialchars($l['item_text']).'</a>';
                            if (!empty($l['subs'])) {
                                foreach ($l['subs'] as $sub) { echo '<a href="'.htmlspecialchars($sub['url']).'" class="ow-sub-link ow-nested-link">'.htmlspecialchars($sub['item_text']).'</a>'; }
                            }
                        }
                    }
                    echo '</div>';
                }
                echo '</div></div>';
            }
            echo '</li>';
        }
        echo '</ul></div>';
    }
}
