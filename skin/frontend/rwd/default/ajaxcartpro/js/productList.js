$j(document).ready(
    function()
    {
        $j('a.button').on('click',
            function(event)
            {
                event.preventDefault()
                setLocation(this.href);
            }
        );
    }
);
