<script src="{{ env('APP_ENV') == 'local' ? asset('js/theme.js') : secure_asset('js/theme.js') }}"></script>
<script type="text/javascript" src="{{ env('APP_ENV') == 'local' ? asset('js/bootstrap-select.js') : secure_asset('js/bootstrap-select.js')}}"></script>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.print.min.js"></script>

<script type="text/javascript" src="{{ env('APP_ENV') == 'local' ? asset('tinymce/js/tinymce/tinymce.min.js') : secure_asset('tinymce/js/tinymce/tinymce.min.js')}}"></script>


<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ env('APP_ENV') == 'local' ? asset('image-upload/dist/js/bootstrap-imageupload.js') : secure_asset('image-upload/dist/js/bootstrap-imageupload.js')}}"></script>
<script src="{{ env('APP_ENV') == 'local' ? asset('js/dropzone.js') : secure_asset('js/dropzone.js') }}" type="text/javascript"></script>

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script src="https://cdn.ckeditor.com/4.14.1/full-all/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select the target element where you want to observe changes
        const targetNode = document.body; // You can change this to a more specific element

        // Set up the configuration for the observer
        const config = {
            childList: true, // Observe direct child addition or removal
            subtree: true    // Also observe all descendants
        };

        // Callback function to execute when mutations are observed
        const callback = function(mutationsList) {
            for (let mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === Node.ELEMENT_NODE && node.classList.contains('cke_notifications_area')) {
                            // Hide the new notification element if it has the exact class 'cke_notifications_area'
                            node.style.display = 'none';
                        }
                    });
                }
            }
        };

        // Create a MutationObserver instance and pass the callback
        const observer = new MutationObserver(callback);

        // Start observing the target node with the specified configuration
        observer.observe(targetNode, config);
    });
</script> 

