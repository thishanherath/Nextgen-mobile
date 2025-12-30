// Admin Mobile Functionality - Shared JS for all admin pages

$(document).ready(function() {
    // Mobile Menu Functionality
    const mobileMenuToggle = $('#mobileMenuToggle');
    const sidebar = $('#sidebar');
    const sidebarOverlay = $('#sidebarOverlay');
    
    // Toggle mobile menu
    mobileMenuToggle.on('click', function() {
        sidebar.toggleClass('show');
        sidebarOverlay.toggleClass('show');
        $('body').toggleClass('menu-open');
    });
    
    // Close menu when clicking overlay
    sidebarOverlay.on('click', function() {
        sidebar.removeClass('show');
        sidebarOverlay.removeClass('show');
        $('body').removeClass('menu-open');
    });
    
    // Close menu when clicking on a link (mobile)
    $('.sidebar .nav-link').on('click', function() {
        if ($(window).width() <= 768) {
            sidebar.removeClass('show');
            sidebarOverlay.removeClass('show');
            $('body').removeClass('menu-open');
        }
    });
    
    // Handle window resize
    $(window).on('resize', function() {
        if ($(window).width() > 768) {
            sidebar.removeClass('show');
            sidebarOverlay.removeClass('show');
            $('body').removeClass('menu-open');
        }
    });
    
    // Touch gesture support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    document.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        if (window.innerWidth <= 768) {
            if (touchEndX < touchStartX - swipeThreshold) {
                // Swipe left - close menu
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.classList.remove('menu-open');
            } else if (touchEndX > touchStartX + swipeThreshold) {
                // Swipe right - open menu
                sidebar.classList.add('show');
                sidebarOverlay.classList.add('show');
                document.body.classList.add('menu-open');
            }
        }
    }
    
    // Prevent body scroll when mobile menu is open
    $('body').on('menu-open', function() {
        $(this).css('overflow', 'hidden');
    }).on('menu-close', function() {
        $(this).css('overflow', 'auto');
    });
    
    // Initialize DataTables with responsive options
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function(row) {
                            var data = row.data();
                            return 'Details for ' + data[0];
                        }
                    }),
                    renderer: $.fn.dataTable.Responsive.renderer.tableAll()
                }
            },
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }
    
    // Responsive table handling for mobile
    $('.table-responsive').on('show.bs.dropdown', function () {
        $('.table-responsive').css("overflow", "inherit");
    });
    
    $('.table-responsive').on('hide.bs.dropdown', function () {
        $('.table-responsive').css("overflow", "auto");
    });
    
    // Add data labels to table cells for mobile responsiveness
    $('.table-responsive table').each(function() {
        $(this).find('thead th').each(function(index) {
            $(this).attr('data-label', $(this).text());
        });
        $(this).find('tbody td').each(function(index) {
            var headerText = $(this).closest('table').find('thead th').eq(index).text();
            $(this).attr('data-label', headerText);
        });
    });
    
    // Enhanced form handling for mobile
    $('form').on('submit', function() {
        if ($(window).width() <= 768) {
            // Show loading state on mobile
            $(this).find('button[type="submit"]').prop('disabled', true);
            $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
        }
    });
    
    // Auto-hide alerts on mobile after 5 seconds
    if ($(window).width() <= 768) {
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
    
    // Enhanced modal handling for mobile
    $('.modal').on('show.bs.modal', function() {
        if ($(window).width() <= 768) {
            $(this).find('.modal-dialog').addClass('modal-fullscreen-sm-down');
        }
    });
    
    // Responsive image handling
    $('img').on('error', function() {
        $(this).attr('src', 'assets/images/placeholder.jpg');
    });
    
    // Enhanced button handling for touch devices
    if ('ontouchstart' in window) {
        $('.btn').on('touchstart', function() {
            $(this).addClass('touch-active');
        }).on('touchend', function() {
            $(this).removeClass('touch-active');
        });
    }
    
    // Auto-refresh dashboard every 5 minutes (only on dashboard page)
    if (window.location.pathname.includes('admin.php')) {
        setTimeout(function() {
            location.reload();
        }, 300000);
    }
    
    // Add loading states to buttons
    $('.btn').on('click', function() {
        if (!$(this).hasClass('no-loading')) {
            $(this).prop('disabled', true);
            $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
        }
    });
    
    // Enhanced search functionality for mobile
    $('.search-input').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        var table = $(this).closest('.card').find('table');
        
        table.find('tbody tr').each(function() {
            var text = $(this).text().toLowerCase();
            if (text.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Responsive chart handling
    if (typeof Chart !== 'undefined') {
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;
    }
    
    // Enhanced dropdown handling for mobile
    $('.dropdown-toggle').on('click', function(e) {
        if ($(window).width() <= 768) {
            e.preventDefault();
            $(this).next('.dropdown-menu').toggleClass('show');
        }
    });
    
    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
    
    // Enhanced pagination for mobile
    $('.pagination').on('click', '.page-link', function(e) {
        if ($(window).width() <= 768) {
            // Add loading state for mobile pagination
            $('.table-responsive').addClass('loading');
            setTimeout(function() {
                $('.table-responsive').removeClass('loading');
            }, 500);
        }
    });
    
    // Responsive file upload handling
    $('input[type="file"]').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $(this).next('.custom-file-label').text(fileName);
        }
    });
    
    // Enhanced tooltip handling for mobile
    if ($(window).width() <= 768) {
        $('[data-bs-toggle="tooltip"]').tooltip({
            trigger: 'click'
        });
    }
    
    // Responsive navigation tabs
    $('.nav-tabs .nav-link').on('click', function(e) {
        if ($(window).width() <= 768) {
            // Scroll to top when switching tabs on mobile
            $('html, body').animate({
                scrollTop: $('.nav-tabs').offset().top - 20
            }, 300);
        }
    });
    
    // Enhanced form validation for mobile
    $('form').on('submit', function(e) {
        if ($(window).width() <= 768) {
            var isValid = true;
            $(this).find('input[required], select[required], textarea[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                    $(this).focus();
                    return false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $(this).find('.is-invalid').first().offset().top - 100
                }, 300);
            }
        }
    });
    
    // Remove invalid class on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Enhanced print functionality
    $('.print-btn').on('click', function() {
        window.print();
    });
    
    // Responsive image gallery
    $('.image-gallery img').on('click', function() {
        if ($(window).width() <= 768) {
            // Show full image in modal on mobile
            var imgSrc = $(this).attr('src');
            var modal = `
                <div class="modal fade" id="imageModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body text-center p-0">
                                <img src="${imgSrc}" class="img-fluid" alt="Full size image">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modal);
            $('#imageModal').modal('show');
            $('#imageModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        }
    });
});

// Utility functions for mobile
function isMobile() {
    return window.innerWidth <= 768;
}

function isTablet() {
    return window.innerWidth > 768 && window.innerWidth <= 1024;
}

function isDesktop() {
    return window.innerWidth > 1024;
}

// Debounce function for performance
function debounce(func, wait, immediate) {
    var timeout;
    return function executedFunction() {
        var context = this;
        var args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    var inThrottle;
    return function() {
        var args = arguments;
        var context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
} 