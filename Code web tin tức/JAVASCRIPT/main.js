
    document.getElementById('toggle-btn').onclick = function() {
        var sidebar = document.getElementById('sidebar');
        // var mainContent = document.getElementById('main-content');
        var mainContent = document.querySelector('.main-content');
        sidebar.classList.toggle('hidden'); 

        if (sidebar.classList.contains('hidden')) {
            mainContent.style.marginLeft = '250px'; 
        } else {
            mainContent.style.marginLeft = '0px'; 
        }
    };
