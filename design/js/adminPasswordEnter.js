let passwdWindow = document.getElementById("password");
let panelPassword = document.getElementById("adminPassword");

passwdWindow.enter = () => {
    panelPassword.submit();
};
