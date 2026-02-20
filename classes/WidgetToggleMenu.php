<?php
namespace OpticWeb\Widgets;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WidgetToggleMenu extends \CE\WidgetBase
{
    public function getName() { return 'ow_toggle_menu'; }
    public function get_name() { return 'ow_toggle_menu'; }
    public function getTitle() { return 'Opticweb Elements Toggle'; }
    public function getIcon() { return 'eicon-menu-bar'; }
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

        // --- 1. SETTINGS: TOGGLE BUTTON ---
        $this->startControlsSection('section_toggle', ['label' => 'Ρυθμίσεις Toggle Button']);
        $this->addControl('toggle_text', ['label' => 'Κείμενο', 'type' => 'text', 'placeholder' => 'MENU']);
        $this->addControl('toggle_icon', [
            'label' => 'Εικονίδιο',
            'type' => 'icons',
            'default' => ['value' => 'fas fa-bars', 'library' => 'fa-solid'],
        ]);
        $this->addControl('toggle_align', [
            'label' => 'Στοίχιση Button',
            'type' => 'select',
            'default' => 'flex-start',
            'options' => ['flex-start' => 'Αριστερά', 'center' => 'Κέντρο', 'flex-end' => 'Δεξιά'],
            'selectors' => ['{{WRAPPER}} .ow-toggle-wrapper' => 'justify-content: {{VALUE}};'],
        ]);
        $this->addControl('menu_position', [
            'label' => 'Θέση Dropdown & Flyout',
            'type' => 'select',
            'default' => 'left',
            'options' => ['left' => 'Αριστερά (Flyout Δεξιά)', 'right' => 'Δεξιά (Flyout Αριστερά)'],
        ]);
        $this->endControlsSection();

        // --- 2. CONTENT (REPEATER) ---
        $this->startControlsSection('section_content', ['label' => 'Δομή Μενού']);
        $repeater = new \CE\Repeater();
        $repeater->addControl('item_type', [
            'label' => 'Τύπος', 'type' => 'select', 'default' => 'main',
            'options' => ['main' => '1. Κεντρικό', 'column' => '2. Flyout Panel', 'link' => '3. Link'],
        ]);
        $repeater->addControl('item_text', ['label' => 'Κείμενο', 'type' => 'text']);
        $repeater->addControl('item_icon', [
            'label' => 'Εικονίδιο',
            'type' => 'icons',
            'default' => ['value' => '', 'library' => ''],
        ]);
        $repeater->addControl('link_type', ['label' => 'Πηγή Link', 'type' => 'select', 'default' => 'custom', 'options' => ['custom' => 'Manual', 'category' => 'Category']]);
        $repeater->addControl('category_id', ['label' => 'Κατηγορία', 'type' => 'select', 'options' => $this->get_ps_categories(), 'condition' => ['link_type' => 'category']]);
        $repeater->addControl('auto_sub', ['label' => 'Auto Subs (2 Levels)', 'type' => 'switcher', 'condition' => ['link_type' => 'category', 'item_type' => 'main']]);
        $repeater->addControl('item_link', ['label' => 'URL', 'type' => 'url', 'condition' => ['link_type' => 'custom']]);
        
        // --- BADGE SETTINGS ---
        $repeater->addControl('badge_text', ['label' => 'Badge Text', 'type' => 'text', 'separator' => 'before']);
        $repeater->addControl('badge_bg', [
            'label' => 'Badge BG',
            'type' => 'color',
            'default' => $brand_color,
            'selectors' => ['{{WRAPPER}} .ow-toggle-list {{CURRENT_ITEM}} .ow-badge' => 'background-color: {{VALUE}} !important;']
        ]);
        $repeater->addControl('badge_color', [
            'label' => 'Badge Color',
            'type' => 'color',
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .ow-toggle-list {{CURRENT_ITEM}} .ow-badge' => 'color: {{VALUE}} !important;']
        ]);
        
        $this->addControl('menu_items', ['label' => 'Items', 'type' => 'repeater', 'fields' => $repeater->getControls(), 'titleField' => '{{{ item_text }}}']);
        $this->endControlsSection();

        // --- 3. STYLE: TOGGLE BUTTON ---
        $this->startControlsSection('style_btn', ['label' => 'Style: Toggle Button', 'tab' => 'style']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'btn_typo', 'selector' => '{{WRAPPER}} .ow-toggle-btn']);
        $this->startControlsTabs('btn_tabs');
        $this->startControlsTab('btn_n', ['label' => 'Normal']);
        $this->addControl('btn_c', ['label' => 'Color', 'type' => 'color', 'default' => '#333', 'selectors' => ['{{WRAPPER}} .ow-toggle-btn' => 'color: {{VALUE}} !important;']]);
        $this->addControl('btn_bg', ['label' => 'Background', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-toggle-btn' => 'background-color: {{VALUE}} !important;']]);
        $this->endControlsTab();
        $this->startControlsTab('btn_h', ['label' => 'Hover']);
        $this->addControl('btn_ch', ['label' => 'Color', 'type' => 'color', 'default' => $brand_color, 'selectors' => ['{{WRAPPER}} .ow-toggle-btn:hover' => 'color: {{VALUE}} !important;']]);
        $this->addControl('btn_bgh', ['label' => 'Background', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-toggle-btn:hover' => 'background-color: {{VALUE}} !important;']]);
        $this->endControlsTab();
        $this->endControlsTabs();
        $this->addControl('btn_rad', ['label' => 'Radius', 'type' => 'dimensions', 'selectors' => ['{{WRAPPER}} .ow-toggle-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->addControl('btn_pad', ['label' => 'Padding', 'type' => 'dimensions', 'default' => ['top'=>'10','right'=>'20','bottom'=>'10','left'=>'20','unit'=>'px'], 'selectors' => ['{{WRAPPER}} .ow-toggle-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->endControlsSection();

        // --- 4. STYLE: MAIN SIDEBAR & L1 ---
        $this->startControlsSection('style_sidebar', ['label' => 'Style: Sidebar & Level 1', 'tab' => 'style']);
        $this->addControl('menu_width', ['label' => 'Width (px)', 'type' => 'slider', 'range' => ['px' => ['min' => 200, 'max' => 500]], 'default' => ['size' => 300], 'selectors' => ['{{WRAPPER}} .ow-toggle-list' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->addControl('menu_bg', ['label' => 'Background', 'type' => 'color', 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .ow-toggle-list' => 'background-color: {{VALUE}} !important;']]);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l1_typo', 'selector' => '{{WRAPPER}} .ow-link-l1']);
        $this->addControl('l1_c', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-link-l1' => 'color: {{VALUE}} !important;']]);
        $this->addControl('indicator_c', ['label' => 'Indicator (Arrow) Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-sub-indicator' => 'color: {{VALUE}} !important;']]);
        $this->endControlsSection();

        // --- 5. STYLE: FLYOUT CARD & L2 ---
        $this->startControlsSection('style_l2', ['label' => 'Style: Flyout & Level 2', 'tab' => 'style']);
        $this->addControl('fly_bg', ['label' => 'Flyout Background', 'type' => 'color', 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .ow-fly-content' => 'background-color: {{VALUE}} !important;']]);
        $this->addGroupControl(\CE\GroupControlBorder::getType(), ['name' => 'fly_border', 'selector' => '{{WRAPPER}} .ow-fly-content']);
        $this->addControl('fly_rad', ['label' => 'Flyout Radius', 'type' => 'dimensions', 'selectors' => ['{{WRAPPER}} .ow-fly-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->addGroupControl(\CE\GroupControlBoxShadow::getType(), ['name' => 'fly_shadow', 'selector' => '{{WRAPPER}} .ow-fly-content']);
        
        $this->addControl('h_l2', ['label' => 'Level 2 Typography (Titles)', 'type' => 'heading', 'separator' => 'before']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l2_typo', 'selector' => '{{WRAPPER}} .ow-fly-title']);
        $this->addControl('l2_c', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-fly-title' => 'color: {{VALUE}} !important;']]);
        $this->addControl('l2_bc', ['label' => 'Underline Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-fly-title' => 'border-bottom-color: {{VALUE}} !important;']]);
        $this->endControlsSection();

        // --- 6. STYLE: LEVEL 3 & 4 (NESTED) ---
        $this->startControlsSection('style_l34', ['label' => 'Style: Levels 3 & 4', 'tab' => 'style']);
        $this->addControl('h_l3', ['label' => 'Level 3 Links', 'type' => 'heading']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l3_typo', 'selector' => '{{WRAPPER}} .ow-fly-link:not(.ow-nested-link)']);
        $this->addControl('l3_c', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-fly-link:not(.ow-nested-link)' => 'color: {{VALUE}} !important;']]);
        
        $this->addControl('h_l4', ['label' => 'Level 4 Links', 'type' => 'heading', 'separator' => 'before']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l4_typo', 'selector' => '{{WRAPPER}} .ow-nested-link']);
        $this->addControl('l4_c', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-nested-link' => 'color: {{VALUE}} !important;']]);
        $this->addControl('l4_mc', ['label' => 'Marker Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-nested-link::before' => 'color: {{VALUE}} !important;']]);
        
        $this->addControl('accent_color', ['label' => 'Hover Accent Color', 'type' => 'color', 'default' => $brand_color, 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .ow-link-l1:hover, {{WRAPPER}} .ow-fly-link:hover' => 'color: {{VALUE}} !important;']]);
        $this->endControlsSection();

        // --- 7. BRANDING (OPTICWEB SUPPORT) ---
        if ($show_branding !== '0') {
            $this->startControlsSection('section_branding', ['label' => 'Opticweb Support & Info']);
            $logo_path = __PS_BASE_URI__ . 'modules/ow_custommenu/logo.png';
            $this->addControl('branding_html', [
                'type' => 'raw_html',
                'raw' => '
                    <div style="background: '.$brand_color.'; padding: 15px; border-radius: 8px; color: #fff; text-align: center;">
                        <img src="'.$logo_path.'" style="max-width: 120px; margin-bottom: 10px;" alt="Opticweb">
                        <p style="margin:0; font-weight: bold; font-size: 14px;">Βασίλης Γαλανάκης</p>
                        <p style="margin:5px 0; font-size: 12px;">Web Design & Development</p>
                        <a href="https://opticweb.gr" target="_blank" style="color: #fff; text-decoration: underline; font-size: 13px;">www.opticweb.gr</a>
                        <div style="margin-top: 10px; font-size: 11px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 10px; opacity: 0.9;">
                            Επικοινωνία: info@opticweb.gr
                        </div>
                    </div>
                ',
            ]);
            $this->endControlsSection();
        }
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        if (empty($settings['menu_items'])) return;

        $id_lang = (int)\Context::getContext()->language->id;
        $id_shop = (int)\Context::getContext()->shop->id;
        $link_service = \Context::getContext()->link;
        $menu_tree = []; $m_idx = -1;

        foreach ($settings['menu_items'] as $item) {
            $item['url'] = ($item['link_type'] === 'category' && (int)$item['category_id'] > 0) ? $link_service->getCategoryLink((int)$item['category_id']) : ($item['item_link']['url'] ?? '#');
            if ($item['item_type'] === 'main') {
                $item['cols'] = [];
                if ($item['link_type'] === 'category' && $item['auto_sub'] === 'yes') {
                    $subs = \Category::getChildren((int)$item['category_id'], $id_lang, true, $id_shop);
                    foreach ($subs as $sub) {
                        $new_col = ['item_text' => $sub['name'], 'url' => $link_service->getCategoryLink((int)$sub['id_category']), 'links' => []];
                        $grand = \Category::getChildren((int)$sub['id_category'], $id_lang, true, $id_shop);
                        foreach ($grand as $gc) {
                            $gc_item = ['item_text' => $gc['name'], 'url' => $link_service->getCategoryLink((int)$gc['id_category']), 'subs' => []];
                            $great = \Category::getChildren((int)$gc['id_category'], $id_lang, true, $id_shop);
                            foreach ($great as $ggc) {
                                $gc_item['subs'][] = ['item_text' => $ggc['name'], 'url' => $link_service->getCategoryLink((int)$ggc['id_category'])];
                            }
                            $new_col['links'][] = $gc_item;
                        }
                        $item['cols'][] = $new_col;
                    }
                }
                $menu_tree[] = $item; $m_idx++;
            } else if ($item['item_type'] === 'column' && $m_idx >= 0) {
                $item['links'] = []; $menu_tree[$m_idx]['cols'][] = $item; $c_idx = count($menu_tree[$m_idx]['cols']) - 1;
            } else if ($item['item_type'] === 'link' && $m_idx >= 0 && isset($c_idx)) {
                $menu_tree[$m_idx]['cols'][$c_idx]['links'][] = $item;
            }
        }

        $id_w = 'owt-' . $this->getId();
        $pos = $settings['menu_position']; 

        echo '<style>
            .ow-toggle-wrapper { display: flex; width: 100%; position: relative; }
            .ow-toggle-btn { display: flex; align-items: center; gap: 10px; cursor: pointer; border: none; background: none; }
            .ow-toggle-list { 
                position: absolute; top: 100%; list-style: none !important; padding: 15px !important; margin: 10px 0 0 !important;
                box-shadow: 0 15px 40px rgba(0,0,0,0.15); border-radius: 8px; display: none; z-index: 9999; min-width: 250px;
                '.($pos === 'right' ? 'right: 0; left: auto;' : 'left: 0; right: auto;').'
            }
            .ow-toggle-wrapper.open > .ow-toggle-list { display: block !important; }
            .ow-item { position: relative; padding: 5px 0; border-bottom: 1px solid #f0f0f0; list-style:none !important; }
            .ow-link-l1 { 
                text-decoration: none !important; display: flex; align-items: center; 
                justify-content: space-between; padding: 8px 0; cursor: pointer; 
            }
            .ow-text-wrapper { position: relative; display: inline-flex; align-items: center; }
            .ow-badge { display: inline-block; font-size: 9px; padding: 2px 5px; border-radius: 3px; margin-left: 5px; z-index: 2; line-height: 1; pointer-events: none; white-space: nowrap; }
            
            .ow-sub-indicator { opacity: 0.5; font-size: 12px; transition: 0.3s; }
            .ow-item.sub-open .ow-sub-indicator { transform: rotate(180deg); opacity: 1; }

            .ow-flyout { 
                position: absolute; top: 0; width: 280px; z-index: 10000; display: none;
                '.($pos === 'right' ? 'right: 100%; padding-right: 15px;' : 'left: 100%; padding-left: 15px;').'
            }
            .ow-item.sub-open > .ow-flyout { display: block !important; }
            .ow-fly-content { padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
            .ow-fly-title { font-weight: bold; display: block; margin-bottom: 10px; border-bottom: 2px solid #eee; text-decoration: none !important; }
            .ow-fly-link { display: block; padding: 6px 0; text-decoration: none !important; transition: 0.2s; }
            .ow-nested-link { padding-left: 15px; font-size: 0.9em; }
            .ow-nested-link::before { content: "\2014"; margin-right: 8px; }

            @media (max-width: 767px) {
                .ow-flyout { position: static; width: 100%; padding: 10px 0 0 15px; }
                .ow-fly-content { padding: 0; box-shadow: none; background: transparent !important; border: none !important; }
                .ow-item.sub-open .ow-sub-indicator { transform: rotate(90deg); }
            }
        </style>';

        echo '<div id="'.$id_w.'" class="ow-toggle-wrapper">
            <button class="ow-toggle-btn" onclick="event.stopPropagation(); this.parentElement.classList.toggle(\'open\');">';
            if (!empty($settings['toggle_icon']['value'])) {
                \CE\IconsManager::renderIcon($settings['toggle_icon'], ['aria-hidden' => 'true']);
            }
            if (!empty($settings['toggle_text'])) echo '<span>'.htmlspecialchars($settings['toggle_text']).'</span>';
        echo '</button>';
        
        echo '<ul class="ow-toggle-list">';
        foreach ($menu_tree as $index => $m) {
            $li_id = $id_w . '-item-' . $index;
            $item_class = 'elementor-repeater-item-' . $m['_id'];
            echo '<li class="ow-item '.$item_class.'" id="'.$li_id.'">';
            $has_subs = !empty($m['cols']);
            
            $click_action = $has_subs ? '
                event.preventDefault(); 
                event.stopPropagation(); 
                var currentLi = document.getElementById(\''.$li_id.'\');
                var parentUl = currentLi.closest(\'.ow-toggle-list\');
                var isOpening = !currentLi.classList.contains(\'sub-open\');
                parentUl.querySelectorAll(\'.ow-item\').forEach(function(li) { li.classList.remove(\'sub-open\'); });
                if(isOpening) currentLi.classList.add(\'sub-open\');
            ' : '';
            
            echo '<a href="'.htmlspecialchars($m['url']).'" class="ow-link-l1" onclick="'.$click_action.'">';
            echo '<span class="ow-text-wrapper">';
            if (!empty($m['item_icon']['value'])) {
                \CE\IconsManager::renderIcon($m['item_icon'], ['aria-hidden' => 'true', 'style' => 'margin-right:8px;']);
            }
            echo htmlspecialchars($m['item_text']);
            if (!empty($m['badge_text'])) echo '<span class="ow-badge">'.htmlspecialchars($m['badge_text']).'</span>';
            echo '</span>';
            if ($has_subs) echo '<i class="fa '.($pos === 'right' ? 'fa-chevron-left' : 'fa-chevron-right').' ow-sub-indicator"></i>';
            echo '</a>';
            
            if ($has_subs) {
                echo '<div class="ow-flyout"><div class="ow-fly-content">';
                foreach ($m['cols'] as $col) {
                    $col_url = isset($col['url']) ? $col['url'] : ($col['item_link']['url'] ?? '#');
                    echo '<a href="'.htmlspecialchars($col_url).'" class="ow-fly-title">'.htmlspecialchars($col['item_text']).'</a>';
                    foreach ($col['links'] as $l) {
                        $l_url = isset($l['url']) ? $l['url'] : ($l['item_link']['url'] ?? '#');
                        echo '<a href="'.htmlspecialchars($l_url).'" class="ow-fly-link">'.htmlspecialchars($l['item_text']).'</a>';
                        if (!empty($l['subs'])) {
                            foreach ($l['subs'] as $sub) echo '<a href="'.htmlspecialchars($sub['url']).'" class="ow-fly-link ow-nested-link">'.htmlspecialchars($sub['item_text']).'</a>';
                        }
                    }
                }
                echo '</div></div>';
            }
            echo '</li>';
        }
        echo '</ul></div>';

        echo '<script>
            document.addEventListener("click", function(e) {
                if (!e.target.closest(".ow-toggle-wrapper")) {
                    document.querySelectorAll(".ow-toggle-wrapper").forEach(el => el.classList.remove("open"));
                }
            });
        </script>';
    }
}
