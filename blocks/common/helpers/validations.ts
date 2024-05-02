const validateVatPT = (number: string): boolean => {
    if (number.length !== 9) {
        return false;
    }

    if (!/^\d+$/.test(number)) {
        return false;
    }

    let digits = number.split('').map(Number);
    let sum = 0;

    for (let i = 0; i < 8; i++) {
        sum += digits[i] * (9 - i);
    }

    let rest = sum % 11;
    let controlDigit = rest === 0 ? 0 : 11 - rest;

    return controlDigit === digits[8];
}

export {validateVatPT};
