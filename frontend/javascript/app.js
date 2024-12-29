document
  .getElementById("loginForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    const loginData = {
      email: email,
      password: password,
    };

    try {
      const response = await fetch(
        "http://localhost:8080/Library_Management_System/backend/api/users/login.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(loginData),
        }
      );

      const responseText = await response.text();
      const data = JSON.parse(responseText);

      if (response.ok && data.token) {
        localStorage.setItem("authToken", data.token);

        localStorage.setItem("user", JSON.stringify(data.user));

        setTimeout(() => {
          window.location.href = "dashboard.html";
        }, 500);
      } else {
        document.getElementById("errorMessage").textContent =
          data.message || "Login failed!";
      }
    } catch (error) {
      console.error("Error:", error);
      document.getElementById("errorMessage").textContent =
        "An error occurred. Please try again.";
    }
  });
