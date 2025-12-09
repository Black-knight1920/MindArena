// Selecting elements
const page = document.querySelector('.page');
const slink = document.querySelector('.signIn');
const loglink = document.querySelector('.signUp');

// Toggle between forms
loglink.addEventListener('click', () => {
    page.classList.add('anim-log');
    page.classList.remove('anim-up');
});

slink.addEventListener('click', () => {
    page.classList.add('anim-up');
    page.classList.remove('anim-log');
});

// Helper: Show + Clear error
const showError = (input, message) => {
    clearError(input);

    const e = document.createElement("span");
    e.classList.add("error-message");
    e.textContent = message;

    input.classList.add("error");
    input.parentElement.appendChild(e);
};

const clearError = (input) => {
    input.classList.remove("error");
    const e = input.parentElement.querySelector(".error-message");
    if (e) e.remove();
};

// Validation rules
const validators = {
    name: (value) => /^[a-zA-Z0-9_]{3,}$/.test(value),
    email: (value) => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value),
    mdp: (value) => /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(value),
    confirm_mdp: (value) => value === document.getElementById("mdp").value,
    date: (value) => {
        const birth = new Date(value);
        const age = new Date().getFullYear() - birth.getFullYear();
        return age >= 18;
    }
};

// Real-time validation per field
document.querySelectorAll(".signup input").forEach(input => {
    input.addEventListener("input", () => {
        const id = input.id;
        const value = input.value.trim();

        if (value === "") {
            clearError(input);
            return;
        }

        if (!validators[id](value)) {
            let msg = {
                name: "Username must be at least 3 characters and only letters, numbers, _ allowed.",
                email: "Please enter a valid email.",
                mdp: "Password must be 8+ chars with at least one uppercase and one digit.",
                confirm_mdp: "Passwords do not match.",
                date: "You must be at least 18 years old."
            };
            showError(input, msg[id]);
        } else {
            clearError(input);
        }
    });
});

// On submit: validate all fields
document.querySelector(".signup form").addEventListener("submit", (e) => {
    let valid = true;

    document.querySelectorAll(".signup input").forEach(input => {
        const id = input.id;
        const value = input.value.trim();

        if (!validators[id](value)) {
            let msg = {
                name: "Invalid username.",
                email: "Invalid email.",
                mdp: "Invalid password.",
                confirm_mdp: "Passwords do not match.",
                date: "Invalid birth date."
            };
            showError(input, msg[id]);
            valid = false;
        }
    });

    if (!valid) e.preventDefault();
});
// --------------------------------------------------------
// SweetAlert handling for login + signup (based on PHP URL)
// --------------------------------------------------------


const params = new URLSearchParams(window.location.search);

if (params.has('error')) {
    const error = params.get('error');

    if (error === 'user_not_found') {
        Swal.fire({
            icon: "error",
            title: "Login Failed",
            text: "Incorrect username or password.",
        }).then(() => {
            // Clean URL after alert is closed
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
    else if (error === 'acces') {
        Swal.fire({
            title: "Account Created",
            icon: "success",
            text: "Your account has been successfully created!",
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
    else if (error === 'e_utiliser') {
        Swal.fire({
            icon: "error",
            title: "Signup Failed",
            text: "This email or username is already used.",
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
    // Add more else if blocks for any other error codes you plan to use
}

