<?php
namespace OpticWeb\Widgets;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WidgetCustomMenu extends \CE\WidgetBase
{
    public function getName() { return 'ow_custom_menu'; }
    public function get_name() { return 'ow_custom_menu'; }
    public function getTitle() { return 'Opticweb Elements Menu'; }
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

        // 1. ΔΙΑΤΑΞΗ
        $this->startControlsSection('section_layout', ['label' => 'Διάταξη & Separators']);
        $this->addControl('main_menu_align', [
            'label' => 'Στοίχιση Μενού',
            'type' => 'select',
            'default' => 'flex-start',
            'options' => ['flex-start' => 'Αριστερά', 'center' => 'Κέντρο', 'flex-end' => 'Δεξιά'],
            'selectors' => ['{{WRAPPER}} .ow-nav-ul' => 'justify-content: {{VALUE}};'],
        ]);
        $this->addControl('show_separators', ['label' => 'Εμφάνιση Διαχωριστικών', 'type' => 'switcher']);
        $this->endControlsSection();

        // 2. ΠΕΡΙΕΧΟΜΕΝΟ
        $this->startControlsSection('section_content', ['label' => 'Δομή Μενού']);
        $repeater = new \CE\Repeater();
        $repeater->addControl('item_type', [
            'label' => 'Τύπος', 'type' => 'select', 'default' => 'main',
            'options' => ['main' => '1. Κεντρικό', 'column' => '2. Στήλη (Column)', 'html' => '2. HTML', 'link' => '3. Link'],
        ]);
        $repeater->addControl('item_text', ['label' => 'Κείμενο', 'type' => 'text', 'condition' => ['item_type!' => 'html']]);
        $repeater->addControl('item_icon', [
            'label' => 'Εικονίδιο',
            'type' => 'icons',
            'default' => ['value' => '', 'library' => ''],
            'condition' => ['item_type!' => 'html'],
        ]);
        $repeater->addControl('item_html', ['label' => 'Custom HTML', 'type' => 'textarea', 'condition' => ['item_type' => 'html']]);
        $repeater->addControl('link_type', ['label' => 'Πηγή Link', 'type' => 'select', 'default' => 'custom', 'options' => ['custom' => 'Manual', 'category' => 'Category']]);
        $repeater->addControl('category_id', ['label' => 'Κατηγορία', 'type' => 'select', 'options' => $this->get_ps_categories(), 'condition' => ['link_type' => 'category']]);
        $repeater->addControl('auto_sub', ['label' => 'Auto Subs (2 Levels)', 'type' => 'switcher', 'condition' => ['link_type' => 'category', 'item_type' => 'main']]);
        $repeater->addControl('is_mega', ['label' => 'Mega Menu', 'type' => 'switcher', 'condition' => ['item_type' => 'main']]);
        $repeater->addControl('dropdown_width', ['label' => 'Πλάτος Dropdown', 'type' => 'slider', 'size_units' => ['px', '%'], 'default' => ['unit' => '%', 'size' => 100], 'condition' => ['item_type' => 'main']]);
        $repeater->addControl('dropdown_align', ['label' => 'Στοίχιση Dropdown', 'type' => 'select', 'default' => 'center', 'options' => ['left' => 'Αριστερά', 'center' => 'Κέντρο', 'right' => 'Δεξιά'], 'condition' => ['item_type' => 'main']]);
        $repeater->addControl('columns_num', ['label' => 'Στήλες', 'type' => 'select', 'default' => '4', 'options' => ['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6'], 'condition' => ['item_type' => 'main']]);
        $repeater->addControl('item_link', ['label' => 'URL', 'type' => 'url', 'condition' => ['link_type' => 'custom']]);
        
        $repeater->addControl('badge_text', ['label' => 'Badge Text', 'type' => 'text', 'separator' => 'before']);
        $repeater->addControl('badge_bg', ['label' => 'Badge BG', 'type' => 'color', 'default' => $brand_color, 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} .ow-badge' => 'background-color: {{VALUE}};']]);
        $repeater->addControl('badge_color', ['label' => 'Badge Color', 'type' => 'color', 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} .ow-badge' => 'color: {{VALUE}};']]);
        $repeater->addControl('badge_padding', ['label' => 'Padding', 'type' => 'dimensions', 'default' => ['top' => '2', 'right' => '5', 'bottom' => '2', 'left' => '5', 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} .ow-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $repeater->addControl('badge_radius', ['label' => 'Radius', 'type' => 'dimensions', 'default' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} .ow-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);

        $this->addControl('menu_items', ['label' => 'Items', 'type' => 'repeater', 'fields' => $repeater->getControls(), 'titleField' => '{{{ item_text }}}']);
        $this->endControlsSection();

        // 3. STYLE GLOBAL
        $this->startControlsSection('section_style_global', ['label' => 'Global & Animations', 'tab' => 'style']);
        $this->addControl('gap', ['label' => 'Gap (px)', 'type' => 'slider', 'default' => ['size' => 25], 'selectors' => ['{{WRAPPER}}' => '--ow-gap: {{SIZE}}px;']]);
        $this->addControl('dropdown_animation', [
            'label' => 'Είσοδος Dropdown', 'type' => 'select', 'default' => \Configuration::get('OW_DROPDOWN_ANIMATION') ?: 'move-up',
            'options' => ['none' => 'None', 'fade' => 'Fade In', 'move-up' => 'Move Up', 'zoom' => 'Zoom In'],
        ]);
        $this->addControl('sep_color', ['label' => 'Separator Color', 'type' => 'color', 'default' => '#e0e0e0', 'selectors' => ['{{WRAPPER}} .ow-menu-item:not(:last-child)::after' => 'background-color: {{VALUE}};']]);
        $this->addControl('sep_height', ['label' => 'Separator Height (%)', 'type' => 'slider', 'default' => ['size' => 40], 'selectors' => ['{{WRAPPER}} .ow-menu-item:not(:last-child)::after' => 'height: {{SIZE}}%;']]);
        $this->endControlsSection();

        // 4. STYLE LEVELS
        $this->startControlsSection('style_l1', ['label' => 'Level 1: Main', 'tab' => 'style']);
        $this->addControl('button_style', ['label' => 'Button Style', 'type' => 'switcher']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l1_typo', 'selector' => '{{WRAPPER}} .ow-main-link']);
        $this->startControlsTabs('l1_tabs');
        $this->startControlsTab('l1_n', ['label' => 'Normal']);
        $this->addControl('l1_c', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-main-link' => 'color: {{VALUE}} !important;']]);
        $this->addControl('l1_bg', ['label' => 'BG', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-main-link' => 'background-color: {{VALUE}};']]);
        $this->endControlsTab();
        $this->startControlsTab('l1_h', ['label' => 'Hover']);
        $this->addControl('l1_ch', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-main-link:hover' => 'color: {{VALUE}} !important;']]);
        $this->addControl('l1_bgh', ['label' => 'BG', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-main-link:hover' => 'background-color: {{VALUE}};']]);
        $this->endControlsTab();
        $this->endControlsTabs();
        $this->addControl('l1_rad', ['label' => 'Radius', 'type' => 'dimensions', 'selectors' => ['{{WRAPPER}} .ow-main-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->addControl('l1_pad', ['label' => 'Padding', 'type' => 'dimensions', 'selectors' => ['{{WRAPPER}} .ow-main-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->endControlsSection();

        $this->startControlsSection('style_card', ['label' => 'Dropdown Card', 'tab' => 'style']);
        $this->addControl('drop_bg', ['label' => 'Background', 'type' => 'color', 'default' => '#ffffff', 'selectors' => ['{{WRAPPER}} .ow-dropdown' => 'background-color: {{VALUE}};']]);
        $this->addGroupControl(\CE\GroupControlBorder::getType(), ['name' => 'drop_border', 'selector' => '{{WRAPPER}} .ow-dropdown']);
        $this->addControl('drop_radius', ['label' => 'Radius', 'type' => 'dimensions', 'default' => ['top'=>'8','right'=>'8','bottom'=>'8','left'=>'8','unit'=>'px'], 'selectors' => ['{{WRAPPER}} .ow-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->addGroupControl(\CE\GroupControlBoxShadow::getType(), ['name' => 'drop_shadow', 'selector' => '{{WRAPPER}} .ow-dropdown']);
        $this->endControlsSection();

        $this->startControlsSection('style_l2', ['label' => 'Level 2: Titles', 'tab' => 'style']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l2_typo', 'selector' => '{{WRAPPER}} .ow-col-title']);
        $this->addControl('l2_c', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-col-title' => 'color: {{VALUE}} !important;']]);
        $this->addControl('l2_bc', ['label' => 'Underline Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-col-title' => 'border-bottom-color: {{VALUE}} !important;']]);
        $this->endControlsSection();

        $this->startControlsSection('style_l34', ['label' => 'Level 3 & 4: Links', 'tab' => 'style']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l3_typo', 'selector' => '{{WRAPPER}} .ow-sub-link:not(.ow-nested-link)']);
        $this->addControl('l3_c', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-sub-link:not(.ow-nested-link)' => 'color: {{VALUE}} !important;']]);
        $this->addControl('l3_ch', ['label' => 'Hover Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-sub-link:not(.ow-nested-link):hover' => 'color: {{VALUE}} !important;']]);
        $this->addControl('h_l4_heading', ['label' => 'Level 4 Nested', 'type' => 'heading', 'separator' => 'before']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'l4_typo', 'selector' => '{{WRAPPER}} .ow-nested-link']);
        $this->addControl('l4_c', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-nested-link' => 'color: {{VALUE}} !important;']]);
        $this->addControl('l4_mc', ['label' => 'Marker Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-nested-link::before' => 'color: {{VALUE}} !important;']]);
        $this->endControlsSection();

        // 5. BRANDING
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

        foreach ($settings['menu_items'] as $item_key => $item) {
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
                            foreach ($great as $ggc) {
                                $gc_item['subs'][] = ['item_text' => $ggc['name'], 'url' => $link_service->getCategoryLink((int)$ggc['id_category'])];
                            }
                            $new_col['links'][] = $gc_item;
                        }
                        $item['cols'][] = $new_col;
                    }
                }
                $menu_tree[] = $item; $m_idx++;
            } else if (($item['item_type'] === 'column' || $item['item_type'] === 'html') && $m_idx >= 0) {
                $item['links'] = []; $menu_tree[$m_idx]['cols'][] = $item; $c_idx = count($menu_tree[$m_idx]['cols']) - 1;
            } else if ($item['item_type'] === 'link' && $m_idx >= 0 && isset($c_idx)) {
                $menu_tree[$m_idx]['cols'][$c_idx]['links'][] = $item;
            }
        }

        $anim = $settings['dropdown_animation'];
        $anim_css = '';
        if ($anim === 'fade') $anim_css = 'opacity: 0;';
        elseif ($anim === 'move-up') $anim_css = 'opacity: 0; transform: translateY(15px);';
        elseif ($anim === 'zoom') $anim_css = 'opacity: 0; transform: scale(0.95);';

        echo '<style>
            .ow-nav-ul { list-style: none; padding: 0; margin: 0; display: flex; align-items: center; position: relative; gap: var(--ow-gap, 25px); }
            .ow-menu-item { position: relative; padding: 15px 0; display: flex; align-items: center; }
            '.($settings['show_separators'] === 'yes' ? '
            .ow-menu-item:not(:last-child)::after { content: ""; position: absolute; right: calc( (var(--ow-gap) / 2) * -1 ); width: 1px; top: 50%; transform: translateY(-50%); display: block; }' : '').'
            .ow-main-link { text-decoration: none; display: flex; align-items: center; gap: 8px; transition: 0.3s; line-height: 1; outline: none; position: relative; }
            .ow-badge { position: absolute; top: -10px; right: -12px; font-size: 9px; font-weight: bold; line-height: 1; white-space: nowrap; z-index: 2; transition: 0.3s; }
            .ow-dropdown { position: absolute; padding: 25px; visibility: hidden; z-index: 1000; transition: all 0.3s ease; top: 100%; '.$anim_css.' }
            .ow-menu-item:hover > .ow-dropdown { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
            .is-mega-parent { position: static !important; }
            .ow-grid { display: grid; gap: 25px; }
            .ow-col-title { font-weight: bold; display: block; margin-bottom: 12px; padding-bottom: 5px; text-decoration: none; border-bottom: 2px solid #eee; }
            .ow-sub-link { display: block; padding: 5px 0; text-decoration: none; color: inherit; }
            .ow-nested-link::before { content: "\2014"; margin-right: 8px; }
        </style>';

        echo '<nav class="ow-nav" role="navigation"><ul class="ow-nav-ul" role="menubar">';
        foreach ($menu_tree as $m) {
            $is_mega = ($m['is_mega'] === 'yes');
            $w = $m['dropdown_width']; $align = $m['dropdown_align'];
            $pos = 'width:'.$w['size'].$w['unit'].';';
            if ($align === 'center') $pos .= 'left: 50%; margin-left: calc('.$w['size'].$w['unit'].' / -2);';
            elseif ($align === 'right') $pos .= 'right: 0;';
            else $pos .= 'left: 0;';

            $item_class = 'elementor-repeater-item-' . $m['_id'];

            echo '<li class="ow-menu-item '.($is_mega ? 'is-mega-parent' : '').' '.$item_class.'" role="none">';
            echo '<a href="'.htmlspecialchars($m['url']).'" class="ow-main-link" role="menuitem">';
            if (!empty($m['item_icon']['value'])) {
                \CE\IconsManager::renderIcon($m['item_icon'], ['aria-hidden' => 'true']);
            }
            echo htmlspecialchars($m['item_text']);
            if (!empty($m['badge_text'])) echo '<span class="ow-badge">'.htmlspecialchars($m['badge_text']).'</span>';
            echo '</a>';

            if (!empty($m['cols'])) {
                echo '<div class="ow-dropdown" style="'.$pos.'" role="menu">';
                echo '<div class="ow-grid" style="grid-template-columns: repeat('.($is_mega ? $m['columns_num'] : '1').', 1fr);">';
                foreach ($m['cols'] as $col) {
                    echo '<div class="ow-col" role="none">';
                    if ($col['item_type'] === 'html') echo '<div class="ow-html">'.$col['item_html'].'</div>';
                    else {
                        echo '<a href="'.htmlspecialchars($col['url']).'" class="ow-col-title">'.htmlspecialchars($col['item_text']).'</a>';
                        foreach ($col['links'] as $l) {
                            $l_url = isset($l['url']) ? $l['url'] : ($l['item_link']['url'] ?? '#');
                            echo '<a href="'.htmlspecialchars($l_url).'" class="ow-sub-link">'.htmlspecialchars($l['item_text']).'</a>';
                            if (!empty($l['subs'])) {
                                foreach ($l['subs'] as $sub) echo '<a href="'.htmlspecialchars($sub['url']).'" class="ow-sub-link ow-nested-link">'.htmlspecialchars($sub['item_text']).'</a>';
                            }
                        }
                    }
                    echo '</div>';
                }
                echo '</div></div>';
            }
            echo '</li>';
        }
        echo '</ul></nav>';
    }
}
