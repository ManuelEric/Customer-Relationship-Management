
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
    }
}