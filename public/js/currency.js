
function currencySymbol(code) {
    switch (code) {
        case 'usd':
            return '$'
            break;
        case 'sgd':
            return 'S$'
            break;
        case 'gbp':
            return '£'
            break;
        case 'aud':
            return 'A$'
            break;
        case 'vnd':
            return '₫'
            break;
        case 'myr':
            return 'MYR'
            break;
        case 'jpy':
            return '¥'
            break;
        case 'cny':
            return 'CN¥'
            break;
        case 'thb':
            return '฿'
            break;
    }
}

function currencyText(code) {
    switch (code) {
        case 'usd':
            return 'US Dollars'
            break;
        case 'sgd':
            return 'Singapore Dollars'
            break;
        case 'gbp':
            return 'British Pounds'
            break;
        case 'aud':
            return 'Australian Dollars'
            break;
        case 'vnd':
            return 'Vietnamese Dong'
            break;
        case 'myr':
            return 'Malaysian Ringgit'
            break;
        case 'jpy':
            return 'Japanese Yen'
            break;
        case 'cny':
            return 'Chinese Yuan'
            break;
        case 'thb':
            return 'Thailand Baht'
            break;
    }
}