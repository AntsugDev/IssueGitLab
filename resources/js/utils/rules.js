export const rulesPassword = {
    min: v => parseInt(v.length) >= 8 || 'Min 8 characters',
    max: v => parseInt(v.length) <= 10 || 'Max 10 characters',
    pattern: v => v.match(/[A_Za_z0_9!£$%&|?*@]/g) || "Password must contain at least one letter, at least one number, at least one symbol and at least one capital letter"

}

export const rulesMatch = (inputOne,inputTwo) => {
    return inputOne === inputTwo || 'Passwords must match'
}

export const rulesNotMatch = (inputOne,inputTwo) => {
    return  inputOne !== inputTwo || 'Passwords do not have to be the same'
}

export const rulesRequired = {
    required: value => !!value || 'Campo obbligatorio',
}

export const rulesEmail = {
    email: v => /.+@.+\..+/.test(v) || 'E-mail non valida',
}
export const rulesMineType = {
    image: (v) => v.length > 0 && ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'].includes( v[0].type) || "Format image not valid (JPEG,JPG,PNG,GIF) "
}
