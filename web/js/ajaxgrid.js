( function( $ ) {
    $.fn.ajaxgrid = function( options ) {        
        var myElement = this;
        var globalData;
        var initialNumberOfRows = 2;
        var settings = $.extend( {
            dataUrl: "",
            sortableColumns: [ "username" ],
            filterableColumns: [ "id_user" ],
        }, options );


        $.ajax( {
            type: "GET",
            url: settings.dataUrl,
            dataType: "json",
            success: function( data ) {
                globalData = data;
                var totalRows = data.length;
                var elementSelect = createSelect();                
                var inputs = [];
                var labels = [];
                $.each( settings.filterableColumns, function( i, value ) {
                    inputs[ i ] = createInput( value );
                    labels[ i ] = createLabel( value );
                } );
                
                var rowsPerPage = Math.ceil( totalRows / initialNumberOfRows );
                var buttons = createButtons( rowsPerPage );

                var table = createTable();
                var thead = $( "<thead>" );
                var tr = $( "<tr>" );
                $.each( data[ 0 ], function( i, value ) {
                    tr.append( $( "<th>", {
                        class: i + " asc"
                    } ).html( i ) );
                } );
                thead.append( tr );
                table.append( thead );              
                var count = 0;
                $.each( data, function( i, value ) {
                    if ( count == initialNumberOfRows ) {
                        return;
                    }
                    var tr = $( "<tr>" );
                    $.each( data[ i ], function( j, value2 ) {
                        tr.append( $( "<td>" ).html( value2 ) );
                    } );
                    table.append( tr );
                    count++;
                } );

                myElement.append( elementSelect );  
                $.each( settings.filterableColumns, function( i, value ) {
                    myElement.append( labels[ i ] );
                    myElement.append( inputs[ i ] );                    
                } );    
                var container =  $( "<div>", { id: "incontainer" } );                      
                container.append( table );
                container.append( buttons );
                myElement.append( container );                
                setData( data );
            }
        } );

        $.each( settings.sortableColumns, function( i, value ) {
            myElement.on( "click.ajaxtable",
                "table#ajaxtable th." + value, sortTable );
        } );
        $.each( settings.filterableColumns, function( i, value ) {
            myElement.on( "input.ajaxtable",
                "input." + value, filtering );
        } );
       
        myElement.on( "change.ajaxtable", "select.selectclass", pagination );
        myElement.on( "click.ajaxtable", "a", pagination );


        function sortTable() {
            $.ajax( {
                type: "GET",
                url: settings.dataUrl,
                dataType: "json",
                success: function( data ) {
                    totalRows = data.length;
                }
            } );
            var element = this;
            element.classList.toggle( "asc" );
            element.classList.toggle( "desc" );
            $.ajax( {
                type: "GET",
                url: settings.dataUrl + "?sortbyfield=" +
                    this.classList[ 0 ] + "&order=" + this.classList[ 1 ],
                dataType: "json",
                success: function( data ) {

                    var pageNumber = $( ".selectclass option:selected" ).val();
                    var rowsPerPage = Math.ceil( totalRows / pageNumber );
                    var buttons = createButtons( rowsPerPage );
                    myElement.find( ".selectclass" ).find( "option" ).filter( function( index ) {
                            return pageNumber === $( this ).text();
                    } ).prop( "selected", "selected" );
                    var table = createTable();

                    var thead = $( "<thead>" );

                    var tr = $( "<tr>" );

                    thAppend( data, tr );


                    thead.append( tr );
                    table.append( thead );
                    trAppend( data, table, pageNumber );
                    var container = myElement.find( "#incontainer" );

                    container.html( "" );                    
                    container.append( table );
                    container.append( buttons );
                    setData( data );
                }
            });
        };
        function filtering() {                       
            var element = this;            
            $.ajax( {
                type: "GET",
                url: settings.dataUrl + "?filterbyfield=" + this.classList[ 0 ] + 
                    "&pattern=%" + $(this).val() + "%",
                dataType: "json",
                success: function( data ) {
                    totalRows = data.length;
                    if ( totalRows == 0 ) {
                        return;
                    }
                    var pageNumber = $( ".selectclass option:selected" ).val();
                    var rowsPerPage = Math.ceil( totalRows / pageNumber );
                    var buttons = createButtons( rowsPerPage );
                    myElement.find( ".selectclass" ).find( "option" ).filter( function( index ) {
                            return pageNumber === $( this ).text();
                    } ).prop( "selected", "selected" );
                    var table = createTable();

                    var thead = $( "<thead>" );
                    var tr = $( "<tr>" );
                    thAppend( data, tr );

                    thead.append( tr );
                    table.append( thead );
                    trAppend( data, table, pageNumber );

                    var container = myElement.find( "#incontainer" );
                    container.html( "" );  
                    if (totalRows === 0) {
                        table.html( "" );
                    }                
                    container.append( table );
                    container.append( buttons );

                    setData( data );
                }
            });
        };

        function createTable() {
            var table = $( "<table>", {
                id: "ajaxtable",
                class: "table table-condensed"
            } );
            return table;
        }

        function createLabel( value ) {
            var label = $( "<label>", {
                text: value               
            } );
            return label;
        }

        function createInput( value ) {
            var input = $( "<input>", {
                class: value              
            } );
            return input;
        }

        function trAppend( data, table, rowsNumber, numberPage ) {
            var count = 0;
            $.each( data, function( i, value ) {
                if ( i < numberPage * rowsNumber ) {
                    return;
                }
                if ( count == rowsNumber ) {
                    return;
                }
                var tr = $( "<tr>" );
                $.each( data[ i ], function( j, value2 ) {
                    tr.append( $( "<td>" ).html( value2 ) );
                } );
                table.append( tr );
                count++;
            } );
        }

        function pagination() {
            
            var pageNumber = $( ".selectclass option:selected" ).val();           
            var rowsPerPage = Math.ceil( getRows( globalData ) / pageNumber );
            var buttons = createButtons( rowsPerPage );

            myElement.find( ".selectclass" ).find( "option" ).filter( function( index ) {
                return pageNumber === $( this ).text();
            } ).prop( "selected", "selected" );
            var table = createTable();
            var thead = $( "<thead>" );
            var tr = $( "<tr>" );

            thAppend( globalData, tr );
            thead.append( tr );
            table.append( thead );
            if ($( this ).prop( "tagName" ) == "SELECT" ) {
                trAppend( globalData, table, pageNumber );
            } else if ( $( this ).prop( "tagName" ) == "A" ) {
                trAppend( globalData, table, pageNumber, $( this ).text() - 1 );
            }

            var container = myElement.find( "#incontainer" );
            container.html( "" );                    
            container.append( table );
            container.append( buttons );
        };

        function thAppend( data, tr ) {
            var count = 0;
            $.each( data[ 0 ], function( i, value ) {
                tr.append( $( "<th>", {
                    class: i + " " + myElement.find( "th" ).eq( count ).attr(
                        "class" ).split( " " )[ 1 ]
                } ).html( i ) );
                count++;
            } );
        }

        function getRows( data ) {
            return data.length;
        }
     
        function createButtons( rowsPerPage ) {
            var buttons = $( "<ul>", {
                class: "pagination"
            } );
            if ( rowsPerPage > 1 ) {
                for ( var u = 0; u < rowsPerPage; u++ ) {
                    buttons.append( $( "<li>" ).append( $( "<a>", {
                        text: u + 1,
                        class: "pag"
                    } ) ) );
                }
            }
            return buttons;
        }

        function createSelect() {

            var elementSelect = $( "<select>", {
                class: "selectclass"
            } );
            elementSelect.append( [ 2, 5, 10, 20, 50 ].map( function ( optionValue ) { 
                return $( "<option>", {
                    text: optionValue
                } )
            } ) );            
            return elementSelect;
        }

        function setData( data ) {
            globalData = data;
        }

        return this;
    };
} )( jQuery );