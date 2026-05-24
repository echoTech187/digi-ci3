/**
 * Global Input Formatting for Nominal (Rupiah), Percentage, Phone, Card, NPWP, and NIK
 */

function formatNPWP(val) {
    if (!val) return '';
    let digits = val.toString().replace(/[^\d]/g, '').substring(0, 15);
    
    let formatted = '';
    if (digits.length > 0) formatted += digits.substring(0, 2);
    if (digits.length > 2) formatted += '.' + digits.substring(2, 5);
    if (digits.length > 5) formatted += '.' + digits.substring(5, 8);
    if (digits.length > 8) formatted += '.' + digits.substring(8, 9);
    if (digits.length > 9) formatted += '-' + digits.substring(9, 12);
    if (digits.length > 12) formatted += '.' + digits.substring(12, 15);
    
    return formatted;
}

function formatNIK(val) {
    if (!val) return '';
    return val.toString().replace(/[^\d]/g, '').substring(0, 16);
}

function formatPhone(val) {
    if (!val) return '';
    let str = val.toString().replace(/[^\d+]/g, '');
    
    // Prevent multiple '+' 
    let hasPlus = str.startsWith('+');
    let digits = str.replace(/[^\d]/g, '');
    
    str = hasPlus ? '+' + digits : digits;

    if (str.length === 0) return hasPlus ? '+' : '';

    // Enforce Indonesian prefix
    if (str.startsWith('+')) {
        if (str === '+') return '+';
        if (str === '+6') return '+6';
        if (!str.startsWith('+62')) {
            str = '+62' + str.substring(1).replace(/^62/, '');
        }
    } else {
        if (str.startsWith('8')) {
            str = '0' + str;
        } else if (str.length > 0 && !str.startsWith('0') && !str.startsWith('62')) {
            // Force 0 if they type any other starting digit
            str = '0' + str;
        }
    }

    // Now format
    let isPlus = str.startsWith('+62');
    let is62 = str.startsWith('62');
    
    let prefix = '';
    let pureDigits = '';
    
    if (isPlus) {
        prefix = '+62';
        pureDigits = str.substring(3);
    } else if (is62) {
        prefix = '62';
        pureDigits = str.substring(2);
    } else {
        prefix = '0';
        pureDigits = str.substring(1);
    }

    let parts = [];
    if (prefix === '0') {
        let firstPart = '0' + pureDigits.substring(0, 3);
        parts.push(firstPart);
        pureDigits = pureDigits.substring(3);
    } else {
        parts.push(prefix);
        let secondPart = pureDigits.substring(0, 3);
        if (secondPart) {
            parts.push(secondPart);
            pureDigits = pureDigits.substring(3);
        }
    }
    
    for (let i = 0; i < pureDigits.length; i += 4) {
        parts.push(pureDigits.substring(i, i + 4));
    }
    
    return parts.filter(p => p !== '').join('-');
}

function formatCard(val) {
    if (val === null || val === undefined || val === '') return '';
    let digits = val.toString().replace(/[^\d]/g, '');
    let parts = [];
    for (let i = 0; i < digits.length; i += 4) {
        parts.push(digits.substring(i, i + 4));
    }
    return parts.join('-');
}

function formatRibuan(val) {
    if (val === null || val === undefined || val === '') return '';
    let str = val.toString();
    
    let cleanStr = str.replace(/[^\d.,]/g, '');
    
    // Check if it's explicitly a decimal starting with 0 (e.g., 012 -> 0.12, 0.12 -> 0.12)
    if (cleanStr.startsWith('0')) {
        if (cleanStr === '0') return '0';
        
        if (cleanStr.startsWith('0.') || cleanStr.startsWith('0,')) {
            let digits = cleanStr.replace(/,/g, '.');
            let parts = digits.split('.');
            return parts[0] + '.' + parts.slice(1).join('');
        }
        
        let digitsOnly = cleanStr.replace(/[^\d]/g, '');
        if (digitsOnly.length > 1) {
            return "0." + digitsOnly.substring(1);
        }
    }
    
    // Normal Rupiah thousands formatting
    let split = cleanStr.replace(/[^,\d]/g, '').split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
}

function formatPercentage(val) {
    if (val === null || val === undefined || val === '') return '';
    let str = val.toString();
    let cleanStr = str.replace(/[^\d.,]/g, '');
    
    if (cleanStr.startsWith('0')) {
        if (cleanStr === '0') return '0';
        if (cleanStr.startsWith('0.') || cleanStr.startsWith('0,')) {
            let digits = cleanStr.replace(/,/g, '.');
            let parts = digits.split('.');
            return parts[0] + '.' + parts.slice(1).join('');
        }
        let digitsOnly = cleanStr.replace(/[^\d]/g, '');
        if (digitsOnly.length > 1) {
            return "0." + digitsOnly.substring(1);
        }
    }
    
    // If not starting with 0, allow standard decimal input (1.5, 10.25)
    let digits = cleanStr.replace(/,/g, '.');
    let parts = digits.split('.');
    return parts[0] + (parts.length > 1 ? '.' + parts.slice(1).join('') : '');
}

(function($) {
    // Intercept jQuery serializeArray to unmask inputs for AJAX requests
    var originalSerializeArray = $.fn.serializeArray;
    $.fn.serializeArray = function() {
        var $form = $(this);
        var $rupiahs = $form.find('.input-rupiah');
        var $percents = $form.find('.input-percentage');
        var $phonesCards = $form.find('.input-phone, .input-card, .input-npwp, .input-nik');

        // Unmask temporarily
        $rupiahs.each(function() {
            var rawVal = this.value;
            $(this).data('temp-mask', rawVal);
            var unmasked;
            if (rawVal.startsWith('0.')) {
                unmasked = rawVal;
            } else {
                unmasked = rawVal.replace(/\./g, '').replace(/,/g, '.');
            }
            this.value = unmasked;
        });

        $percents.each(function() {
            var rawVal = this.value;
            $(this).data('temp-mask', rawVal);
            this.value = rawVal.replace(/,/g, '.');
        });

        $phonesCards.each(function() {
            var rawVal = this.value;
            $(this).data('temp-mask', rawVal);
            this.value = rawVal.replace(/[.\-]/g, '');
        });

        // Perform standard serialize
        var result = originalSerializeArray.call(this);

        // Revert instantly
        $rupiahs.add($percents).add($phonesCards).each(function() {
            var original = $(this).data('temp-mask');
            if (original !== undefined) this.value = original;
        });

        return result;
    };

    // Intercept jQuery .val() to auto-format dynamically populated fields
    var originalVal = $.fn.val;
    $.fn.val = function(value) {
        if (arguments.length === 0) {
            return originalVal.call(this);
        }
        
        if (typeof value === 'function') {
            return originalVal.apply(this, arguments);
        }

        return this.each(function() {
            let $el = $(this);
            let valToSet = value;
            
            if (valToSet !== undefined && valToSet !== null && valToSet !== '') {
                if ($el.hasClass('input-rupiah')) {
                    valToSet = formatRibuan(valToSet);
                } else if ($el.hasClass('input-percentage')) {
                    valToSet = formatPercentage(valToSet);
                } else if ($el.hasClass('input-phone')) {
                    valToSet = formatPhone(valToSet);
                } else if ($el.hasClass('input-card')) {
                    valToSet = formatCard(valToSet);
                } else if ($el.hasClass('input-npwp')) {
                    valToSet = formatNPWP(valToSet);
                } else if ($el.hasClass('input-nik')) {
                    valToSet = formatNIK(valToSet);
                }
            }
            
            originalVal.call($el, valToSet);
        });
    };
})(jQuery);

$(document).ready(function() {
    
    $(document).on('input', '.input-rupiah', function() {
        let formatted = formatRibuan(this.value);
        if (this.value !== formatted) this.value = formatted;
    });

    $(document).on('input', '.input-percentage', function() {
        let formatted = formatPercentage(this.value);
        if (this.value !== formatted) this.value = formatted;
    });

    $(document).on('input', '.input-phone', function() {
        let formatted = formatPhone(this.value);
        if (this.value !== formatted) this.value = formatted;
    });

    $(document).on('input', '.input-card', function() {
        let formatted = formatCard(this.value);
        if (this.value !== formatted) this.value = formatted;
    });

    $(document).on('input', '.input-npwp', function() {
        let formatted = formatNPWP(this.value);
        if (this.value !== formatted) this.value = formatted;
    });

    $(document).on('input', '.input-nik', function() {
        let formatted = formatNIK(this.value);
        if (this.value !== formatted) this.value = formatted;
    });

    // Intercept form submission to unmask inputs before sending
    $(document).on('submit', 'form', function(e) {
        var $form = $(this);
        var $rupiahs = $form.find('.input-rupiah');
        var $percents = $form.find('.input-percentage');
        var $phonesCards = $form.find('.input-phone, .input-card, .input-npwp, .input-nik');

        
        $rupiahs.each(function() {
            var rawVal = this.value;
            $(this).data('original-mask', rawVal);
            var unmasked;
            if (rawVal.startsWith('0.')) {
                unmasked = rawVal;
            } else {
                unmasked = rawVal.replace(/\./g, '').replace(/,/g, '.');
            }
            this.value = unmasked;
        });

        $percents.each(function() {
            var rawVal = this.value;
            $(this).data('original-mask', rawVal);
            this.value = rawVal.replace(/,/g, '.');
        });

        $phonesCards.each(function() {
            var rawVal = this.value;
            $(this).data('original-mask', rawVal);
            this.value = rawVal.replace(/[.\-]/g, ''); // Remove hyphens and dots for backend (phone/card/npwp)
        });

        // Revert back shortly after serialize/submit is fired
        setTimeout(function() {
            $rupiahs.each(function() {
                var original = $(this).data('original-mask');
                if (original !== undefined) this.value = original;
            });
            $percents.each(function() {
                var original = $(this).data('original-mask');
                if (original !== undefined) this.value = original;
            });
            $phonesCards.each(function() {
                var original = $(this).data('original-mask');
                if (original !== undefined) this.value = original;
            });
        }, 100);
    });
});
