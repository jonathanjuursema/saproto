<!-- Extra style //-->

<style type="text/css">
    #header {
        background-image: url('{{ asset('images/application/headerbg.jpg') }}');
    }

    /*.green {
        color: #C1FF00 !important;
    }*/

    .white {
        color: #fff !important;
    }

    .gray {
        color: #333 !important;
    }

    .dark {
        background-color: #333 !important;
    }

    .light {
        background-color: #fafafa !important;
    }

    .center {
        text-align: center;
    }

    .member {
        height: 40px;
        line-height: 30px;
        padding: 5px 20px 5px 5px;
        text-align: left;
    }

    .member:nth-child(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .member-picture {
        width: 30px;
        height: 30px;
        background-size: cover;
        background-position: center center;
        float: left;
        background-color: #333;
        border-radius: 15px;
        margin-right: 10px;
    }

</style>

<!-- Bootstrap overrides //-->
<style type="text/css">

    .borderless td, .borderless th {
        border: none !important;
    }

</style>

<!-- jQuery UI Theme //-->
<style>

    .ui-autocomplete {
        position: absolute;
        top: 0;
        left: 0;

        list-style-type: none;

        background-color: #fff;
        padding: 10px 0;
        border-top: 5px solid #C1FF00;

        box-shadow: 0px 0px 20px -7px #000;
    }

    .ui-menu-item {
        padding: 5px 20px;

        transition: all 0.2s;
    }

    .ui-menu-item:hover {
        background-color: rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

</style>

<!-- Lightbox Bugfixes //-->
<style>

    /* Navbar had a higher z-index */
    .chocolat-wrapper {
        z-index: 2000;
    }

</style>
