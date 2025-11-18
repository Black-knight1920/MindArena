document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const token = params.get("token");

    if (token) {
        const tokenInput = document.getElementById("token");
        if (tokenInput) tokenInput.value = token;
    } else {
        Swal.fire({
            icon: "error",
            title: "Invalid Link",
            text: "Missing token in the URL."
        }).then(() => {
            window.location.href = "../login/login.html";
        });
    }

    // Handle error/success alerts
    if (params.has('error')) {
        const error = params.get('error');
        switch (error) {
            case 'success':
                Swal.fire({
                    icon: "success",
                    title: "Password Changed",
                    text: "You can now log in with your new password"
                }).then(() => {
                    window.location.href = "../login/login.html";
                });
                break;
            case 'invalid_token':
                Swal.fire({ icon:"error", title:"Invalid Token", text:"The reset link is invalid or has already been used." });
                break;
            case 'expired_token':
                Swal.fire({ icon:"error", title:"Expired Token", text:"The reset link has expired. Please request a new one." });
                break;
            case 'missing_data':
                Swal.fire({ icon:"error", title:"Missing Information", text:"Token or password is missing." });
                break;
            case 'db_error':
            case 'query_error':
                Swal.fire({ icon:"error", title:"Server Error", text:"A database error occurred. Please try again later." });
                break;
        }
    }
});
