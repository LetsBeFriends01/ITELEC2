function toggleForm() {
  const register = document.getElementsByClassName("register")[0];
  const login = document.getElementsByClassName("login")[0];
  const toggle = document.getElementsByClassName("toggle")[0];
  const toggleDesc = document.getElementsByClassName("toggle-desc")[0];

  console.log("what");
  if (register.style.display === "block") {
    register.style.display = "none";
    login.style.display = "block";
    toggleDesc.innerHTML = "Don't have a account yet?";
    toggle.innerHTML = "Sign Up";
  } else {
    login.style.display = "none";
    register.style.display = "block";
    toggleDesc.innerHTML = "Already have an account?";
    toggle.innerHTML = "Sign In";
  }
}
