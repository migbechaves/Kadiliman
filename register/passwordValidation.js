function validatePassword(password) {
    const minLength = 8;
    const hasUpperCase = /[A-Z]/;
    const hasLowerCase = /[a-z]/;
    const hasNumber = /\d/;
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/;

    let feedback = "";

    if (password.length < minLength) {
        feedback += "<span style='color: red;'>Password must be at least  characters long.</span><br>";
    } else {
        feedback += "<span style='color: green;'>Password length is sufficient.</span><br>";
    }

    if (!hasUpperCase.test(password)) {
        feedback += "<span style='color: red;'>Password must contain at least one uppercase letter.</span><br>";
    } else {
        feedback += "<span style='color: green;'>Password contains an uppercase letter.</span><br>";
    }

    if (!hasLowerCase.test(password)) {
        feedback += "<span style='color: red;'>Password must contain at least one lowercase letter.</span><br>";
    } else {
        feedback += "<span style='color: green;'>Password contains a lowercase letter.</span><br>";
    }

    if (!hasNumber.test(password)) {
        feedback += "<span style='color: red;'>Password must contain at least one number.</span><br>";
    } else {
        feedback += "<span style='color: green;'>Password contains a number.</span><br>";
    }

    if (!hasSpecialChar.test(password)) {
        feedback += "<span style='color: red;'>Password must contain at least one special character.</span><br>";
    } else {
        feedback += "<span style='color: green;'>Password contains a special character.</span><br>";
    }

    return feedback;
}