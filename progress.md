__________     __________
|Employee| Or   principal
‾‾‾‾‾‾‾‾‾‾     ‾‾‾‾‾‾‾‾‾‾
add  ✅
edit ✅
add roles ✅
remove roles ✅
add temp role ❌

    add base calendar ✅
    edit base calendar ✅

    ____________
    |supervisor|
    ‾‾‾‾‾‾‾‾‾‾‾‾
    add classes to supervisor ✅
    remove classes from supervisor ✅ (merged with the add classes route)

    add test_form ✅
    edit test_form ✅
    maybe: remove test_form with good care ❌
    add test for a class ✅
    edit test of a class ✅
    add test marks of a class ✅
    edit test marks of a class  ✅

    see the base calendar for a specific subject ✅

    add daily absences of students ✅  (you can call it multiple times if you want to add more)
    get student's absences ✅  
    justify student's absence ✅   (an absence is justified only when a justification is provided)
    edit absence justification ✅  (just provide the new justification)

    add notes on a student ( like : bad behavior,bad mark,good mark,good behavior,etc..) ❌

    send cutome messages to parents ❌

    add subjects_progress daily ❌

    get the students in a class ✅
    
    get the test marks of a class ✅

    get test type from id ✅

    get the remaining students who's marks wasn't inserted yet for this test ✅
    (will be used when getting the students names to insert their marks)

    view test marks ✅

    view test info ❌

    view all tests (search + filters) ✅
    ___________
    |secretary|
    ‾‾‾‾‾‾‾‾‾‾‾
    add a direct or candidate student ✅
    edit a direct or candidate student ✅
    regenerate account password of a student ✅
    get candidates of some grade with their final_result_percentage to determin who can be registered as student  ✅ (can be called multiple times) 
    Using previous route, give me the ids of the candidates that should become students  ❌  (abd:working on this);
    search for a student ✅
     
    _________
    |teacher|
    ‾‾‾‾‾‾‾‾‾
    add subjects and classes to teacher ✅
    remove subjects and classes from teacher ✅ (merged with add route)
    send an advice to a student ❌

    ________________
    |bus_supervisor|
    ‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾
    get the students he supervises ❌
    manage going trip ( student got on/off the bus ) ❌
    manage returning trip ( student got off the bus ) ❌
    
    ___________
    |bus_admin|
    ‾‾‾‾‾‾‾‾‾‾‾
    add the addresses that the school wants to reach ❌
    edit an address ❌
    remove an address with good care ❌
    add a bus ❌
    add bus path with price for every address it reaches ❌
    edit bus path and prices ❌
    assign a bus to a bus_supervisor ❌
    remove a bus from bus_supervisor ❌
    calculate the price every student should pay (just hit the button in the right time) ❌
    
    ____________
    |accountant|
    ‾‾‾‾‾‾‾‾‾‾‾‾
    add payment for student ❌
    edit payment of student ❌
    monitor the finiance stuff ❌

    



