(function() {
    window.SIMAP.dataTable = function(tableId, searchInputId) {
        var table = document.getElementById(tableId);
        var input = document.getElementById(searchInputId);
        
        if (!table || !input) return;

        input.addEventListener('keyup', function() {
            var filter = input.value.toLowerCase();
            var rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                if (text.indexOf(filter) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    };
})();