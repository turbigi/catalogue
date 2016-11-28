( function( $ ) {
    $.fn.ajaxgrid = function( options ) {        
        var 
            myElement = this,        
            totalRowsInDb,
            sortByField = "",
            filterByField = "",
            pattern = "",
            order = "",
            numberOfRows = 2, 
            numberOfPage = 0,   
            isFiltering = false,  
            filteringIsEnd = true,
            initialNumberOfRows = 2,
            rowsInFiltering;
       
        var settings = $.extend( {
            dataUrl: "",
            sortableColumns: [ "username" ],
            filterableColumns: [ "id_user" ],
            urlAddUser: "",
        }, options );
        var table = createTable();
        var thead = $( "<thead>" );
        var tbody = $( "<tbody>" );
        $.ajax( {
            type: "GET",
            url: settings.dataUrl,
            dataType: "json",
            success: function( returnedJson ) {
                setTotalRows( returnedJson.length );
                   
                var tr = $( "<tr>", { class: "filters" } );
                var orderClass;
                if ( order === "" ) {
                    orderClass = "";
                } else {
                    orderClass = order;
                }                     
                var count = 0;              
                $.each( returnedJson[ 0 ], function( i, value ) {
                    var th = $( "<th>", {
                        class: settings.sortableColumns[ count ] + " asc"
                    } );
                    th.append( $( "<input>", { type: "text", class: "form-control " + 
                        settings.filterableColumns[ count ], placeholder: settings.sortableColumns[ count ], 
                        disabled: !isFiltering 
                    } ) );
                    tr.append( th );
                    count++;
                } );
                thead.append( tr );
                table.append( thead );   
            },
            error: function() {  
                myElement.append( $( "<p>" ).text( "Data not found!" ) );
            }           
        } ); 

        table.append( tbody );
        var buttons = $( "<ul>", {
            class: "pagination pull-left"
        } );

        var elementSelect = createSelect(); 
        myElement.append( elementSelect );  
      
        var divRow =  $( "<div>", { class: "row" } ); 
        var filterablePanel =  $( "<div>", { class: "panel panel-primary filterable table-responsive" } ); 
        var headingPanel =  $( "<div>", { class: "panel-heading" } ); 
        var titlePanel =  $( "<h3>", { class: "panel-title" } ); 
        var pullRightPanel =  $( "<div>", { class: "pull-right" } );
        var filterButton =  $( "<button>", { class: "btn btn-default btn-xs btn-filter" } );
        var addButton =   $( "<button>", { class: "btn btn-default btn-xs btn-add-user", style: "margin-left: 10px;" } );        
        var linkNewUser = $( "<a>", { class: "url-add-user", href: settings.urlAddUser } );      
        filterButton.on("click" , function () { 
            isFiltering = !isFiltering;               
            var panel = $( this ).parents( '.filterable' ),
            filters = panel.find( '.filters input' ),
            tbody = panel.find( '.table tbody' );
            if (filters.prop( 'disabled' ) == true ) {
                filters.prop( 'disabled', false );
                filters.first().focus();
            } else {
                filters.val( '' ).prop( 'disabled', true );
                tbody.find( '.no-result' ).remove();
                tbody.find( 'tr' ).show();
            }
        } );

        titlePanel.text( "Dashboard" );
        filterButton.text( "Filter" );
        addButton.text( "New" );
        linkNewUser.append( addButton ); 

        pullRightPanel.append( filterButton );
        pullRightPanel.append( linkNewUser );
        headingPanel.append( titlePanel );      
        headingPanel.append( pullRightPanel );
        filterablePanel.append( headingPanel );
        divRow.append( filterablePanel );
        myElement.append( divRow ); 
        filterablePanel.append( table );
        myElement.append( buttons );
        myElement.find( ".selectclass" ).find( "option" ).filter( function( index ) {
            return 2 === $( this ).text();
        } ).prop( "selected", "selected" );     

        numberOfRows = $( ".selectclass option:selected" ).val();

        var url = settings.dataUrl + "?rows=" + numberOfRows;
        sendRequest( url );
       
        $.each( settings.sortableColumns, function( indexInArray, value ) {
            myElement.on( "click.ajaxtable",
                "table#ajaxtable th." + value, sortTable );
        } );

        $.each( settings.filterableColumns, function( indexInArray, value ) {
            myElement.on( "input.ajaxtable",
                "input." + value, filtering );
        } );
       
        myElement.on( "change.ajaxtable", "select.selectclass", pagination );
        myElement.on( "click.ajaxtable", "a", pagination );


        function sortTable() {
            if ( isFiltering ) return;
            var element = this;            
            element.classList.toggle( "asc" );
            element.classList.toggle( "desc" );
            order = this.classList[ 1 ];
            sortByField = this.classList[ 0 ];
            url = settings.dataUrl + "?sortbyfield=" +
                this.classList[ 0 ] + "&order=" + this.classList[ 1 ] + "&rows=" + numberOfRows;
            sendRequest( url );
        };

        function filtering() {                       
            var element = this;
            sortByField = "";            
            order = "";
            filterByField = this.classList[ 1 ];            
            pattern = $( this ).val();

             if ( isFiltering ) {               
                filteringIsEnd = false;
                url = settings.dataUrl + "?filterbyfield=" + this.classList[ 1 ] + 
                    "&pattern=%" + $( this ).val() + "%" + "&rows=" + totalRowsInDb;
            } else {
                filteringIsEnd = true;               
                url = settings.dataUrl + "?sortbyfield=" + sortByField + 
                    "&order=" + order + "&rows=" + numberOfRows;
            } 
            sendRequest( url ); 
        };

        function createTable() {
            var table = $( "<table>", {
                id: "ajaxtable",
                class: "table"
            } );
            return table;
        }

        function trAppend( jsonFromDb, table, rowsNumber, numberPage ) {
            var count = 0;
            $.each( jsonFromDb, function( index, value ) {
                if ( index < numberPage * rowsNumber ) {
                    return;
                }
                if ( count == rowsNumber ) {
                    return;
                }
                var tr = $( "<tr>" );
                $.each( jsonFromDb[ index ], function( internalIndex, internalValue ) {
                    tr.append( $( "<td>" ).html( internalValue ) );
                } );
                table.append( tr );
                count++;
            } );
        }

        function pagination() { 

            if ( $( this ).prop( "tagName" ) == "SELECT" ) {
                var pageNumber = $( ".selectclass option:selected" ).val();
                myElement.find( ".selectclass" ).find( "option" ).filter( function( index ) {
                    return pageNumber === $( this ).text();
                } ).prop( "selected", "selected" );  

                numberOfRows = pageNumber;

                if ( isFiltering ) {
                    url = settings.dataUrl + "?filterbyfield=" + filterByField + 
                        "&pattern=%" + pattern + "%" + "&rows=" + numberOfRows;                       
                    sendRequest( url ); 
                } else {
                    url = settings.dataUrl + "?sortbyfield=" +
                        sortByField + "&order=" + order + "&rows=" + numberOfRows;                   
                    sendRequest( url ); 
                }   

            } else if ( $( this ).prop( "tagName" ) == "A" ) {
                numberOfPage = $( this ).text() - 1;                 
                url = settings.dataUrl + "?sortbyfield=" +
                    sortByField + "&order=" + order + "&rows=" + numberOfRows + "&page=" + numberOfPage + "&filterbyfield=" + filterByField + 
                    "&pattern=%" + pattern + "%";
                sendRequest( url );               
            }  
        };

        function thAppend( jsonFromDb, tr ) {
            var count = 0;
            $.each( jsonFromDb[ 0 ], function( keyOfJson , value ) {
                tr.append( $( "<th>", {
                    class: keyOfJson + " " + myElement.find( "th" ).eq( count ).attr(
                        "class" ).split( " " )[ 1 ]
                } ).html( keyOfJson ) );
                count++;
            } );
        }
     
        function createButtons( rowsPerPage ) {
            if ( rowsPerPage > 1 ) {
                for ( var i = 0; i < rowsPerPage; i++ ) {
                    buttons.append( $( "<li>" ).append( $( "<a>", {
                        text: i + 1,
                        class: "pag"
                    } ) ) );
                }
            }
            return buttons;
        }

        function createSelect() {

            var elementSelect = $( "<select>", {
                class: "selectclass form-control"
            } );
            elementSelect.append( [ 2, 5, 10, 20, 50 ].map( function ( optionValue ) { 
                return $( "<option>", {
                    text: optionValue
                } )
            } ) );            
            return elementSelect;
        }

        function setTotalRows( totalRowsCount ) {
            totalRowsInDb = totalRowsCount;
        }

        function sendRequest( url ) {
            $.ajax( {
                type: "GET",
                url: url,
                dataType: "json",
                success: function( returnedJson ) {                
                    buttons.html( "" );
                    tbody.html( "" );
                    var totalRows = returnedJson.length;                                                 
                    var rowsPerPage = Math.ceil( totalRowsInDb / numberOfRows );  
                    if ( !filteringIsEnd ) {
                        rowsInFiltering = totalRows;
                    }
                    if ( isFiltering ) {
                        rowsPerPage = Math.ceil( rowsInFiltering / numberOfRows );
                    }                
                    filteringIsEnd = true;
                    buttons.append( createButtons( rowsPerPage ) );
                              
                    var count = 0;
                    $.each( returnedJson, function( keyOfJson, value ) {
                        if ( count == numberOfRows ) {
                            return;
                        }
                        var tr = $( "<tr>" );
                        $.each( returnedJson[ keyOfJson ], function( internalKeyOfJson, internalValue ) {
                            tr.append( $( "<td>" ).html( internalValue ) );
                        } );
                        tbody.append( tr );                        
                        count++;
                    } );
                },
                error: function() {  
                    myElement.append( $( "<p>" ).text( "Data not found!" ) );
                }
            } );
        }

        return this;
    };
} )( jQuery );