<?php
namespace OpticWeb\Widgets;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WidgetPopUp extends \CE\WidgetBase
{
    public function getName() { return 'ow_popup'; }
    public function get_name() { return 'ow_popup'; }
    public function getTitle() { return 'Opticweb Elements Pop-up (No Scroll)'; }
    public function getIcon() { return 'eicon-lightbox'; }
    public function getCategories() { return ['opticweb']; }

    protected function get_cms_pages()
    {
        $id_lang = (int)\Context::getContext()->language->id;
        $pages = \CMS::getCMSPages($id_lang, null, true);
        $options = ['0' => 'Επιλέξτε Σελίδα...'];
        foreach ($pages as $page) {
            $options[$page['id_cms']] = $page['meta_title'] . ' (ID: ' . $page['id_cms'] . ')';
        }
        return $options;
    }

    protected function registerControls()
    {
        // --- 1. SETTINGS ---
        $this->startControlsSection('section_settings', ['label' => 'Ρυθμίσεις Εμφάνισης']);
        $this->addControl('auto_open', [
            'label' => 'Αυτόματο Άνοιγμα', 'type' => 'select', 'default' => 'no',
            'options' => ['no' => 'Όχι (Με Κουμπί)', 'yes' => 'Ναι (Αυτόματα)'],
        ]);
        $this->addControl('test_mode', [
            'label' => 'Λειτουργία Δοκιμής', 'type' => 'switcher', 'default' => 'no',
            'description' => 'Αγνοεί το ιστορικό cookies.',
            'condition' => ['auto_open' => 'yes'],
        ]);
        $this->addControl('auto_open_delay', [
            'label' => 'Καθυστέρηση (ms)', 'type' => 'number', 'default' => 2000,
            'condition' => ['auto_open' => 'yes'],
        ]);
        
        $this->addControl('btn_text', ['label' => 'Κείμενο Κουμπιού', 'type' => 'text', 'default' => 'Άνοιγμα Pop-up', 'condition' => ['auto_open' => 'no']]);
        $this->addControl('btn_icon', ['label' => 'Εικονίδιο', 'type' => 'icon', 'condition' => ['auto_open' => 'no']]);
        $this->addControl('btn_align', [
            'label' => 'Στοίχιση', 'type' => 'select', 'default' => 'flex-start',
            'options' => ['flex-start' => 'Αριστερά', 'center' => 'Κέντρο', 'flex-end' => 'Δεξιά'],
            'selectors' => ['{{WRAPPER}} .ow-popup-trigger-wrapper' => 'justify-content: {{VALUE}};'],
            'condition' => ['auto_open' => 'no'],
        ]);
        $this->endControlsSection();

        // --- 2. CONTENT ---
        $this->startControlsSection('section_content', ['label' => 'Περιεχόμενο']);
        $this->addControl('content_source', [
            'label' => 'Πηγή', 'type' => 'select', 'default' => 'custom',
            'options' => ['custom' => 'Manual Editor', 'cms' => 'PrestaShop CMS Page'],
        ]);
        $this->addControl('cms_id', ['label' => 'Επιλογή Σελίδας', 'type' => 'select', 'options' => $this->get_cms_pages(), 'condition' => ['content_source' => 'cms']]);
        $this->addControl('pop_title', ['label' => 'Τίτλος', 'type' => 'text', 'default' => 'Πληροφορίες', 'condition' => ['content_source' => 'custom']]);
        $this->addControl('pop_content', ['label' => 'Περιεχόμενο', 'type' => 'wysiwyg', 'default' => '<p>Κείμενο...</p>', 'condition' => ['content_source' => 'custom']]);
        $this->endControlsSection();

        // --- 3. STYLE: BUTTON ---
        $this->startControlsSection('style_btn', ['label' => 'Style: Κουμπί', 'tab' => 'style', 'condition' => ['auto_open' => 'no']]);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'btn_typo', 'selector' => '{{WRAPPER}} .ow-popup-btn']);
        $this->startControlsTabs('btn_tabs');
        $this->startControlsTab('btn_n', ['label' => 'Normal']);
        $this->addControl('btn_c', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-popup-btn' => 'color: {{VALUE}} !important;']]);
        $this->addControl('btn_bg', ['label' => 'Background', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-popup-btn' => 'background-color: {{VALUE}} !important;']]);
        $this->endControlsTab();
        $this->startControlsTab('btn_h', ['label' => 'Hover']);
        $this->addControl('btn_ch', ['label' => 'Color', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-popup-btn:hover' => 'color: {{VALUE}} !important;']]);
        $this->addControl('btn_bgh', ['label' => 'Background', 'type' => 'color', 'selectors' => ['{{WRAPPER}} .ow-popup-btn:hover' => 'background-color: {{VALUE}} !important;']]);
        $this->endControlsTab();
        $this->endControlsTabs();
        $this->addControl('btn_rad', ['label' => 'Radius', 'type' => 'dimensions', 'separator' => 'before', 'selectors' => ['{{WRAPPER}} .ow-popup-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->addControl('btn_pad', ['label' => 'Padding', 'type' => 'dimensions', 'selectors' => ['{{WRAPPER}} .ow-popup-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->endControlsSection();

        // --- 4. STYLE: CARD ---
        $this->startControlsSection('style_pop', ['label' => 'Style: Pop-up Card', 'tab' => 'style']);
        $this->addControl('card_width', ['label' => 'Width (px)', 'type' => 'slider', 'range' => ['px' => ['min' => 300, 'max' => 1200]], 'default' => ['size' => 900], 'selectors' => ['#ow-modal-{{ID}} .ow-popup-card' => 'max-width: {{SIZE}}{{UNIT}};']]);
        
        $this->addControl('iframe_auto_height', [
            'label' => 'Αυτόματο Ύψος (Smart)', 'type' => 'switcher', 'default' => 'yes',
            'description' => 'Προσαρμογή ύψους ανάλογα με το περιεχόμενο.',
            'condition' => ['content_source' => 'cms']
        ]);

        $this->addControl('card_height', [
            'label' => 'Χειροκίνητο Ύψος (px)', 'type' => 'slider', 'range' => ['px' => ['min' => 200, 'max' => 1000]], 'default' => ['size' => 600], 
            'selectors' => ['#ow-modal-{{ID}} .ow-popup-iframe' => 'height: {{SIZE}}{{UNIT}};'], 
            'condition' => ['content_source' => 'cms', 'iframe_auto_height' => ''] 
        ]);

        $this->addControl('overlay_bg', ['label' => 'Overlay Color', 'type' => 'color', 'default' => 'rgba(0,0,0,0.8)', 'selectors' => ['#ow-modal-{{ID}}' => 'background-color: {{VALUE}} !important;']]);
        $this->addControl('card_bg', ['label' => 'Card BG', 'type' => 'color', 'default' => '#ffffff', 'selectors' => ['#ow-modal-{{ID}} .ow-popup-card' => 'background-color: {{VALUE}} !important;']]);
        $this->addControl('card_rad', ['label' => 'Radius', 'type' => 'dimensions', 'default' => ['top'=>'15','right'=>'15','bottom'=>'15','left'=>'15','unit'=>'px'], 'selectors' => ['#ow-modal-{{ID}} .ow-popup-card, #ow-modal-{{ID}} .ow-popup-iframe' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->addGroupControl(\CE\GroupControlBoxShadow::getType(), ['name' => 'card_shadow', 'selector' => '#ow-modal-{{ID}} .ow-popup-card']);
        $this->endControlsSection();

        // --- 5. STYLE: TYPOGRAPHY ---
        $this->startControlsSection('style_typo', ['label' => 'Style: Τίτλος & Κείμενο', 'tab' => 'style', 'condition' => ['content_source' => 'custom']]);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'title_t', 'selector' => '#ow-modal-{{ID}} .ow-popup-body h3']);
        $this->addControl('title_c', ['label' => 'Title Color', 'type' => 'color', 'selectors' => ['#ow-modal-{{ID}} .ow-popup-body h3' => 'color: {{VALUE}} !important;']]);
        $this->addControl('h_cont', ['label' => 'Περιεχόμενο', 'type' => 'heading', 'separator' => 'before']);
        $this->addGroupControl(\CE\GroupControlTypography::getType(), ['name' => 'cont_t', 'selector' => '#ow-modal-{{ID}} .ow-popup-content, #ow-modal-{{ID}} .ow-popup-content p']);
        $this->addControl('cont_c', ['label' => 'Content Color', 'type' => 'color', 'selectors' => ['#ow-modal-{{ID}} .ow-popup-content, #ow-modal-{{ID}} .ow-popup-content p' => 'color: {{VALUE}} !important;']]);
        $this->endControlsSection();

        // --- 6. BRANDING ---
        $this->startControlsSection('section_branding', ['label' => 'Opticweb Support & Info']);
        $logo_path = __PS_BASE_URI__ . 'modules/ow_custommenu/logo.png';
        $this->addControl('branding_html', ['type' => 'raw_html', 'raw' => '<div style="background:#268CCD;padding:15px;border-radius:8px;color:#fff;text-align:center;"><img src="'.$logo_path.'" style="max-width:120px;margin-bottom:10px;"><p style="margin:0;font-weight:bold;">Βασίλης Γαλανάκης</p><a href="https://opticweb.gr" target="_blank" style="color:#fff;text-decoration:underline;">www.opticweb.gr</a></div>']);
        $this->endControlsSection();
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        $id_w = $this->getId();
        $source = $settings['content_source'];
        
        echo '<style>
            #ow-modal-'.$id_w.' { position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: none; align-items: center; justify-content: center; z-index: 999999; }
            #ow-modal-'.$id_w.'.active { display: flex; animation: owFadeIn 0.3s; }
            
            #ow-modal-'.$id_w.' .ow-popup-card { 
                width: 100%; position: relative; display: flex; flex-direction: column; 
                background: #fff; overflow: visible !important; 
            }
            #ow-modal-'.$id_w.' .ow-popup-close { 
                position: absolute; top: -15px; right: -15px; width: 35px; height: 35px; 
                background: #268CCD; color: #fff; border-radius: 50%; display: flex; 
                align-items: center; justify-content: center; cursor: pointer; z-index: 9999999; 
                border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.2); font-weight: bold; 
            }
            #ow-modal-'.$id_w.' .ow-popup-body { padding: 0; width: 100%; height: 100%; overflow: hidden; }
            .ow-popup-iframe { width: 100%; border: none; display: block; min-height: 200px; }
            
            .ow-popup-btn { display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: 0.3s; }
            .ow-popup-trigger-wrapper { display: flex; width: 100%; }
            @keyframes owFadeIn { from { opacity: 0; } to { opacity: 1; } }
            .ow-error-msg { color: #cc0000; padding: 20px; text-align: center; border: 1px dashed #cc0000; background: #ffeeee; }
        </style>';

        if ($settings['auto_open'] !== 'yes') {
            echo '<div class="ow-popup-trigger-wrapper"><button class="ow-popup-btn" onclick="document.getElementById(\'ow-modal-'.$id_w.'\').classList.add(\'active\'); document.body.style.overflow=\'hidden\';">';
            if (!empty($settings['btn_icon']['value'])) echo '<i class="'.htmlspecialchars($settings['btn_icon']['value']).'"></i>';
            echo '<span>'.htmlspecialchars($settings['btn_text']).'</span></button></div>';
        }

        echo '<div id="ow-modal-'.$id_w.'" class="ow-modal-overlay" onclick="if(event.target === this) { this.classList.remove(\'active\'); document.body.style.overflow=\'auto\'; }">
            <div class="ow-popup-card">
                <div class="ow-popup-close" onclick="document.getElementById(\'ow-modal-'.$id_w.'\').classList.remove(\'active\'); document.body.style.overflow=\'auto\';">&times;</div>
                <div class="ow-popup-body">';

        if ($source === 'cms' && !empty($settings['cms_id'])) {
            $id_cms = (int)$settings['cms_id'];
            $link = new \Link();
            $cms_url = $link->getCMSLink($id_cms);
            if (strpos($cms_url, '?') !== false) {
                $cms_url .= '&content_only=1';
            } else {
                $cms_url .= '?content_only=1';
            }

            // *** FIX HORIZONTAL SCROLLBAR: html, body { overflow-x: hidden } ***
            $onload_script = "try {
                /* 1. Hide Breadcrumbs & Kill Scrollbar */
                var css = 'html, body { overflow-x: hidden !important; width: 100% !important; margin: 0 !important; } .breadcrumb, #wrapper .breadcrumb, nav.breadcrumb { display: none !important; } #wrapper { padding-top: 0 !important; margin-top: 0 !important; }';
                var style = this.contentWindow.document.createElement('style');
                style.type = 'text/css';
                style.appendChild(document.createTextNode(css));
                this.contentWindow.document.head.appendChild(style);
            ";

            if ($settings['iframe_auto_height'] === 'yes') {
                $onload_script .= "
                    /* 2. Auto Resize Height */
                    var iframe = this;
                    setTimeout(function(){
                        try {
                            var h = iframe.contentWindow.document.body.scrollHeight;
                            if(h > 0) iframe.style.height = (h + 20) + 'px';
                        } catch(e){}
                    }, 300);
                ";
            }
            $onload_script .= "} catch(e){}";

            echo '<iframe src="'.$cms_url.'" class="ow-popup-iframe" loading="lazy" onload="'.$onload_script.'"></iframe>';

        } else {
            echo '<div style="padding:30px;">';
            if (!empty($settings['pop_title'])) echo '<h3 style="margin-top:0;">'.htmlspecialchars($settings['pop_title']).'</h3>';
            echo $settings['pop_content'];
            echo '</div>';
        }

        echo '</div></div></div>';

        if ($settings['auto_open'] === 'yes') {
            $test = ($settings['test_mode'] === 'yes') ? 'true' : 'false';
            echo '<script>(function(){
                const pid = "ow_pop_'.$id_w.'";
                const isTest = '.$test.';
                if (isTest || !localStorage.getItem(pid)) {
                    setTimeout(() => {
                        const m = document.getElementById("ow-modal-'.$id_w.'");
                        if (m) { 
                            m.classList.add("active"); 
                            document.body.style.overflow = "hidden";
                            if (!isTest) localStorage.setItem(pid, "1");
                        }
                    }, '.(int)$settings['auto_open_delay'].');
                }
            })();</script>';
        }
    }
}