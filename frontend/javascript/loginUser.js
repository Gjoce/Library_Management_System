document
  .getElementById("loginForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    // Get form values
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    // Prepare data to send
    const loginData = {
      EMAIL: email,
      PASSWORD: password,
    };

    try {
      const response = await fetch(
        "https://online-library-management-60dd26a214d9.herokuapp.com/api/users/loginUser.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(loginData),
        }
      );

      const result = await response.json();

      if (response.ok) {
        // Store the JWT token in localStorage
        localStorage.setItem("jwt", result.token);
        localStorage.setItem("userId", result.user_id); // Store user ID as well

        // Redirect to the user dashboard or home page
        window.location.href = "dash.html"; // Change this to your user dashboard page
      } else {
        // Handle error
        document.getElementById("errorMessage").innerText = result.message;
      }
    } catch (error) {
      console.error("Error:", error);
      document.getElementById("errorMessage").innerText =
        "An error occurred. Please try again later.";
    }
  });
