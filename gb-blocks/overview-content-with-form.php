<?php
// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;
global $contentRowsInPage,$curContIndex,$sectionID;

$overview = get_field('overview');
$short_code = get_field('short_code');

// Set placeholders for admin view
if(is_admin()) {
    if(empty($overview)) {
        $overview = "Overview text goes here..";
    }
}
?>
<!--overview-content-with-form Start Here-->
<div class="overview-content-with-form">
    <div class="overview-content-with-form-in fw">
        <div class="overview-text-content">
            <?php echo wpautop($overview); ?>
        </div>
    </div>
</div>
<!--overview-content-with-form End Here-->

<div class="contact-form">
    <div class="contact-form-in fw">
        <?php 
        if(!empty($short_code)) {
            echo do_shortcode($short_code);
        } elseif(is_admin()) {
            echo '<p>Short code goes here.</p>';
        }
        ?>
    </div>
</div>

<script>
        class CF7MultiSelectEnhancer {
            constructor(selectElement) {
                this.originalSelect = selectElement;
                this.customMultiselect = document.getElementById('customMultiselect');
                this.displayElement = this.customMultiselect.querySelector('.multiselect-display .display-text');
                this.dropdownMenu = this.customMultiselect.querySelector('.dropdown-menu');
                this.tagsContainer = document.getElementById('selectedTags');
                
                this.init();
            }

            init() {
                this.originalSelect.classList.add('enhanced');
                
                this.buildDropdownOptions();
                
                this.syncFromOriginalSelect();
                
                this.addEventListeners();
                
                this.updateDisplay();
            }

            buildDropdownOptions() {
                const options = this.originalSelect.querySelectorAll('option');
                this.dropdownMenu.innerHTML = '';
                
                options.forEach((option, index) => {
            
                    if (index === 0 && option.value === '') return;
                    
                    const dropdownOption = document.createElement('div');
                    dropdownOption.className = 'dropdown-option';
                    dropdownOption.dataset.value = option.value;
                    
                    dropdownOption.innerHTML = `
                        <div class="option-checkbox"></div>
                        <span>${option.textContent}</span>
                    `;
                    
                    this.dropdownMenu.appendChild(dropdownOption);
                });
            }

            addEventListeners() {

                this.customMultiselect.querySelector('.multiselect-display').addEventListener('click', () => {
                    this.toggleDropdown();
                });


                this.customMultiselect.querySelector('.multiselect-display').addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.toggleDropdown();
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!this.customMultiselect.contains(e.target)) {
                        this.closeDropdown();
                    }
                });

                this.dropdownMenu.addEventListener('click', (e) => {
                    const option = e.target.closest('.dropdown-option');
                    if (option) {
                        this.toggleOption(option);
                    }
                });

                this.originalSelect.addEventListener('change', () => {
                    this.syncFromOriginalSelect();
                });
            }

            toggleDropdown() {
                this.customMultiselect.classList.toggle('open');
            }

            closeDropdown() {
                this.customMultiselect.classList.remove('open');
            }

            toggleOption(optionElement) {
                const value = optionElement.dataset.value;
                const originalOption = this.originalSelect.querySelector(`option[value="${value}"]`);
                
                if (originalOption) {
                    originalOption.selected = !originalOption.selected;
                    
                    this.originalSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    this.updateDisplay();
                }
            }

            syncFromOriginalSelect() {
                const selectedOptions = Array.from(this.originalSelect.selectedOptions);
                
                this.dropdownMenu.querySelectorAll('.dropdown-option').forEach(option => {
                    const value = option.dataset.value;
                    const isSelected = selectedOptions.some(opt => opt.value === value);
                    
                    option.classList.toggle('selected', isSelected);
                    option.querySelector('.option-checkbox').classList.toggle('checked', isSelected);
                });
                
                this.updateDisplay();
            }

            updateDisplay() {
                const selectedOptions = Array.from(this.originalSelect.selectedOptions);
                const selectedValues = selectedOptions.map(opt => opt.value).filter(val => val !== '');
                
                if (selectedValues.length === 0) {
                    this.displayElement.textContent = 'ZOEK GEMEENTE';
                    this.customMultiselect.querySelector('.multiselect-display').classList.remove('has-selection');
                } else {
                    this.displayElement.textContent = `${selectedValues.length} gemeente${selectedValues.length > 1 ? 's' : ''} geselecteerd`;
                    this.customMultiselect.querySelector('.multiselect-display').classList.add('has-selection');
                }
                
                this.updateTags(selectedOptions);
            }

            updateTags(selectedOptions) {
                this.tagsContainer.innerHTML = '';
                
                selectedOptions.forEach(option => {
                    if (option.value === '') return; 
                    
                    const tag = document.createElement('div');
                    tag.className = 'location-tag';
                    tag.innerHTML = `
                        ${option.textContent}
                        <span class="tag-remove" data-value="${option.value}">×</span>
                    `;
                    
                    tag.querySelector('.tag-remove').addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.removeTag(option.value);
                    });
                    
                    this.tagsContainer.appendChild(tag);
                });
            }

            removeTag(value) {
                const option = this.originalSelect.querySelector(`option[value="${value}"]`);
                if (option) {
                    option.selected = false;
                    this.originalSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    this.updateDisplay();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.querySelector('.location-input');
            if (selectElement) {
                new CF7MultiSelectEnhancer(selectElement);
            }
        });

        document.addEventListener('wpcf7submit', function(event) {
            console.log('Form submitted with selected values:', 
                Array.from(event.target.querySelector('.location-input').selectedOptions)
                    .map(opt => opt.value)
                    .filter(val => val !== '')
            );
        });

</script>

<?php
if(intval($contentRowsInPage['overview-content-with-form']) == 0 || is_admin()){
    if(file_exists(get_template_directory().'/css/overview-content-with-form.css')){
        echo '<style>';
        include(get_template_directory().'/css/overview-content-with-form.css');
        echo '</style>';
    }    
}
$contentRowsInPage['overview-content-with-form'] = intval($contentRowsInPage['overview-content-with-form'])+1;