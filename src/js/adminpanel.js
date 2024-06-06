document.addEventListener("DOMContentLoaded", function() {
    // Function to handle form submission
    function handleFormSubmission(event) {
        event.preventDefault(); // Prevent default form submission
        
        // You can add your form handling logic here
        // For example, you can use AJAX to submit form data to the server
        
        // For demonstration purposes, let's just log the form data to the console
        //const formData = new FormData(event.target);
        //for (const pair of formData.entries()) {
        //    console.log(pair[0] + ': ' + pair[1]);
        //}
        
        // Reset the form after submission
        event.target.reset();
    }

    // Add event listeners to all forms on the page
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', handleFormSubmission);
    });
});
