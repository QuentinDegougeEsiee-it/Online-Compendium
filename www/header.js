document.addEventListener('DOMContentLoaded', function() {
    const userBtn = document.querySelector('.user-dropdown-btn');
    const userContent = document.querySelector('.user-dropdown-content');
    
    if (userBtn && userContent) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userContent.classList.toggle('show');
        });
        
        document.addEventListener('click', function() {
            userContent.classList.remove('show');
        });
    }
});