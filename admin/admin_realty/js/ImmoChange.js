function ImmoSaveInDB(param, immo_id) {
    var query;
    switch(param) {
        case 1:
            if(parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type='+$('#ImmoChangeType').val();
                query += ', k_isf_new='+$('#ImmoChangeClass').val();
                query += ', k_isf_material='+$('#ImmoChangeMaterial').val();
                query += ', k_isf_floor='+$('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all='+$('#ImmoChangeFloorAll').val();
                query += ', k_isf_eq='+$('#ImmoChangeEQ').val();
                query += ', k_isf_rooms='+parseInt($('#ImmoChangeRooms').val(),10);
                query += ', k_isf_area_all='+parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live='+parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_kitchen='+parseFloat($('#ImmoChangeAreaKitchen').val());
                query += ', k_isf_san='+$('#ImmoChangeSan').val();
                query += ', k_isf_balcony='+$('#ImmoChangeBalcony').val();
                query += ', k_isf_phone_stat='+CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_price='+parseInt($('#ImmoChangePrice').val(),10);
                query += ', k_isf_quickly='+CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch='+CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange='+CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit='+CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents='+CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned='+CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_privat='+CheckOrNot('ImmoChangePrivat');
                query += ', k_isf_description="'+$('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contact_name="'+$('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contacts="'+$('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query +=' WHERE k_isf_id='+immo_id;
                $.post("inc/admin_functions.php",{
                    SaveInDBParam:query
                } ,
                function(data)
                {
                    alert(data);
                })
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 2:
            if(parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type='+$('#ImmoChangeType').val();
                query += ', k_isf_new='+$('#ImmoChangeClass').val();
                query += ', k_isf_material='+$('#ImmoChangeMaterial').val();
                query += ', k_isf_floor_all='+$('#ImmoChangeFloorAll').val();
                query += ', k_isf_eq='+$('#ImmoChangeEQ').val();
                query += ', k_isf_rooms='+parseInt($('#ImmoChangeRooms').val(),10);
                query += ', k_isf_area_all='+parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live='+parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_land='+parseFloat($('#ImmoChangeAreaLand').val());
                query += ', k_isf_phone_stat='+CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_price='+parseInt($('#ImmoChangePrice').val(),10);
                query += ', k_isf_quickly='+CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch='+CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange='+CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit='+CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents='+CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned='+CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_privat='+CheckOrNot('ImmoChangePrivat');
                query += ', k_isf_description="'+$('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contact_name="'+$('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contacts="'+$('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query +=' WHERE k_isf_id='+immo_id;
                $.post("inc/admin_functions.php",{
                    SaveInDBParam:query
                } ,
                function(data)
                {
                    alert(data);
                })
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 3:
            if(parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type='+$('#ImmoChangeType').val();
                query += ', k_isf_new='+$('#ImmoChangeClass').val();
                query += ', k_isf_material='+$('#ImmoChangeMaterial').val();
                query += ', k_isf_floor='+$('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all='+$('#ImmoChangeFloorAll').val();
                query += ', k_isf_eq='+$('#ImmoChangeEQ').val();
                query += ', k_isf_area_all='+parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live='+parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_kitchen='+parseFloat($('#ImmoChangeAreaKitchen').val());
                query += ', k_isf_san='+$('#ImmoChangeSan').val();
                query += ', k_isf_balcony='+$('#ImmoChangeBalcony').val();
                query += ', k_isf_phone_stat='+CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_security='+CheckOrNot('ImmoChangeSecurity');
                query += ', k_isf_internet='+CheckOrNot('ImmoChangeInternet');
                query += ', k_isf_price='+parseInt($('#ImmoChangePrice').val(),10);
                query += ', k_isf_quickly='+CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch='+CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange='+CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit='+CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents='+CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned='+CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_description="'+$('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contact_name="'+$('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contacts="'+$('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query +=' WHERE k_isf_id='+immo_id;
                $.post("inc/admin_functions.php",{
                    SaveInDBParam:query
                } ,
                function(data)
                {
                    alert(data);
                })
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 4:
            if(parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type='+$('#ImmoChangeType').val();
                query += ', k_isf_new='+$('#ImmoChangeClass').val();
                query += ', k_isf_floor='+$('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all='+$('#ImmoChangeFloorAll').val();
                query += ', k_isf_area_all='+parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_price='+parseInt($('#ImmoChangePrice').val(),10);
                query += ', k_isf_quickly='+CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch='+CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange='+CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit='+CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents='+CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned='+CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_privat='+CheckOrNot('ImmoChangePrivat');
                query += ', k_isf_description="'+$('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contact_name="'+$('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contacts="'+$('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query +=' WHERE k_isf_id='+immo_id;
                $.post("inc/admin_functions.php",{
                    SaveInDBParam:query
                } ,
                function(data)
                {
                    alert(data);
                })
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 5:
            if(parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type='+$('#ImmoChangeType').val();
                query += ', k_isf_area_land='+parseFloat($('#ImmoChangeAreaLand').val());
                query += ', k_isf_price='+parseInt($('#ImmoChangePrice').val(),10);
                query += ', k_isf_quickly='+CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch='+CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_exchange='+CheckOrNot('ImmoChangeExchange');
                query += ', k_isf_credit='+CheckOrNot('ImmoChangeCredit');
                query += ', k_isf_documents='+CheckOrNot('ImmoChangeDoc');
                query += ', k_isf_owned='+CheckOrNot('ImmoChangeOwned');
                query += ', k_isf_privat='+CheckOrNot('ImmoChangePrivat');
                query += ', k_isf_description="'+$('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contact_name="'+$('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contacts="'+$('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query +=' WHERE k_isf_id='+immo_id;
                $.post("inc/admin_functions.php",{
                    SaveInDBParam:query
                } ,
                function(data)
                {
                    alert(data);
                })
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 6:
            if(parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type='+$('#ImmoChangeType').val();
                query += ', k_isf_floor='+$('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all='+$('#ImmoChangeFloorAll').val();
                query += ', k_isf_rooms='+parseInt($('#ImmoChangeRooms').val(),10);
                query += ', k_isf_area_all='+parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live='+parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_kitchen='+parseFloat($('#ImmoChangeAreaKitchen').val());
                query += ', k_isf_san='+$('#ImmoChangeSan').val();
                query += ', k_isf_balcony='+$('#ImmoChangeBalcony').val();
                query += ', k_isf_internet='+CheckOrNot('ImmoChangeInternet');
                query += ', k_isf_balcony_gl='+CheckOrNot('ImmoChangeBalconyGl');
                query += ', k_isf_furniture='+CheckOrNot('ImmoChangeFurniture');
                query += ', k_isf_fridge='+CheckOrNot('ImmoChangeFridge');
                query += ', k_isf_washing='+CheckOrNot('ImmoChangeWashing');
                query += ', k_isf_microwave='+CheckOrNot('ImmoChangeMicrowave');
                query += ', k_isf_tv='+CheckOrNot('ImmoChangeTV');
                query += ', k_isf_ctv='+CheckOrNot('ImmoChangeCTV');
                query += ', k_isf_stove='+CheckOrNot('ImmoChangeStove');
                query += ', k_isf_plastic_windows='+CheckOrNot('ImmoChangePlastic');
                query += ', k_isf_utilities='+$('#ImmoChangeUtil').val();
                query += ', k_isf_price='+parseInt($('#ImmoChangePrice').val(),10);
                query += ', k_isf_quickly='+CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch='+CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_description="'+$('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contact_name="'+$('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contacts="'+$('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query +=' WHERE k_isf_id='+immo_id;
                $.post("inc/admin_functions.php",{
                    SaveInDBParam:query
                } ,
                function(data)
                {
                    alert(data);
                })
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 7:
            if(parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type='+$('#ImmoChangeType').val();
                query += ', k_isf_material='+$('#ImmoChangeMaterial').val();
                query += ', k_isf_floor_all='+$('#ImmoChangeFloorAll').val();
                query += ', k_isf_rooms='+parseInt($('#ImmoChangeRooms').val(),10);
                query += ', k_isf_area_all='+parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_area_live='+parseFloat($('#ImmoChangeAreaLive').val());
                query += ', k_isf_area_land='+parseFloat($('#ImmoChangeAreaLand').val());
                query += ', k_isf_phone_stat='+CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_furniture='+CheckOrNot('ImmoChangeFurniture');
                query += ', k_isf_fridge='+CheckOrNot('ImmoChangeFridge');
                query += ', k_isf_washing='+CheckOrNot('ImmoChangeWashing');
                query += ', k_isf_microwave='+CheckOrNot('ImmoChangeMicrowave');
                query += ', k_isf_tv='+CheckOrNot('ImmoChangeTV');
                query += ', k_isf_ctv='+CheckOrNot('ImmoChangeCTV');
                query += ', k_isf_utilities='+$('#ImmoChangeUtil').val();
                query += ', k_isf_price='+parseInt($('#ImmoChangePrice').val(),10);
                query += ', k_isf_quickly='+CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch='+CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_description="'+$('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contact_name="'+$('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contacts="'+$('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query +=' WHERE k_isf_id='+immo_id;
                $.post("inc/admin_functions.php",{
                    SaveInDBParam:query
                } ,
                function(data)
                {
                    alert(data);
                })
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 8:
            if(parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type='+$('#ImmoChangeType').val();
                query += ', k_isf_new='+$('#ImmoChangeClass').val();
                query += ', k_isf_material='+$('#ImmoChangeMaterial').val();
                query += ', k_isf_floor='+$('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all='+$('#ImmoChangeFloorAll').val();
                query += ', k_isf_area_all='+parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_phone_stat='+CheckOrNot('ImmoChangePhoneStat');
                query += ', k_isf_security='+CheckOrNot('ImmoChangeSecurity');
                query += ', k_isf_internet='+CheckOrNot('ImmoChangeInternet');
                query += ', k_isf_utilities='+$('#ImmoChangeUtil').val();
                query += ', k_isf_price='+parseInt($('#ImmoChangePrice').val(),10);
                query += ', k_isf_quickly='+CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch='+CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_description="'+$('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contact_name="'+$('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contacts="'+$('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query +=' WHERE k_isf_id='+immo_id;
                $.post("inc/admin_functions.php",{
                    SaveInDBParam:query
                } ,
                function(data)
                {
                    alert(data);
                })
            } else {
                alert('\u0412ведите цену!');
            }
            break;
        case 9:
            if(parseInt($('#ImmoChangePrice').val().length) > 0) {
                query = 'UPDATE k_immovables_sell SET';
                query += ' k_isf_immovable_type='+$('#ImmoChangeType').val();
                query += ', k_isf_floor='+$('#ImmoChangeFloor').val();
                query += ', k_isf_floor_all='+$('#ImmoChangeFloorAll').val();
                query += ', k_isf_area_all='+parseFloat($('#ImmoChangeAreaAll').val());
                query += ', k_isf_utilities='+$('#ImmoChangeUtil').val();
                query += ', k_isf_price='+parseInt($('#ImmoChangePrice').val(),10);
                query += ', k_isf_quickly='+CheckOrNot('ImmoChangeQuickly');
                query += ', k_isf_merch='+CheckOrNot('ImmoChangeMerch');
                query += ', k_isf_description="'+$('#ImmoChangeDescr').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contact_name="'+$('#ImmoChangeContName').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query += ', k_isf_contacts="'+$('#ImmoChangeContacts').val().replace(/'/g, "\\'").replace(/\"/g,'\\"')+'"';
                query +=' WHERE k_isf_id='+immo_id;
                $.post("inc/admin_functions.php",{
                    SaveInDBParam:query
                } ,
                function(data)
                {
                    alert(data);
                })
            } else {
                alert('\u0412ведите цену!');
            }
            break;
    }
}