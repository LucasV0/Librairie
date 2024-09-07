//fonctionnalité de validation en temps réel pour un champ de mot de passe

document.addEventListener('DOMContentLoaded', function() {
    var passwordField = document.querySelector('.password-field');

    passwordField.addEventListener('input', function() {
        var password = this.value;
        var rules = {
            uppercase: /(?=.*?[A-Z])/,
            lowercase: /(?=.*?[a-z])/,
            number: /(?=.*?[0-9])/,
            special: /(?=.*?[#?!@$%^&*-])/,
            length: /.{14,}/
        };

        for (var rule in rules) {
            var element = document.getElementById(rule);
            if (rules[rule].test(password)) {
                element.classList.remove('invalid');
                element.classList.add('valid');
            } else {
                element.classList.remove('valid');
                element.classList.add('invalid');
            }
        }
    });
});

