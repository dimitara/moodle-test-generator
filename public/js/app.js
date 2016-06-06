var questions = [];
var contexts = null;
var qArray = [];

var build = function(){
    pageCounter = 0;
    questionsPerPage = 5;

    function question (questionId, questionTitle, questionType){
        
        this.questionId = questionId;
        this.questionTitle = questionTitle;
        this.questionType = questionType;
        this.isChecked = false;

        
        
        this.add = function (isChecked) {
            var questionTable = document.getElementById("questionTable");
            
            //TABLE ROW
            var qTableRow = document.createElement("tr");
            qTableRow.setAttribute("class", "qTr");
            qTableRow.setAttribute("cellspacing", "0");
            qTableRow.setAttribute("id", questionId);
            questionTable.appendChild(qTableRow)
            
            //CHECKBOX
            var qCheckBox = document.createElement("input")
            qCheckBox.setAttribute("class", "qCheckBox");
            qCheckBox.setAttribute("type", "checkbox");
            qCheckBox.setAttribute("name", "qCheckbox");
            qCheckBox.setAttribute("value", questionId);
            qCheckBox.checked = isChecked;
            qCheckBox.onclick = function () {
                for (var i = 0; i < qArray.length; i++) {
                    if (qArray[i].questionId == questionId){
                        if (qArray[i].isChecked == true)
                            qArray[i].isChecked = false;
                        else
                            qArray[i].isChecked = true;
                    }
                }
            }
            
            //QUESTION NAME
            var qInputContainer = document.createElement("td");
            qInputContainer.setAttribute("class", "qInputContainer");
            qInputContainer.appendChild(qCheckBox);

            //QUESTION NAME
            var qName = document.createElement("td");
            qName.setAttribute("class", "qName");
            var qNameText = document.createTextNode(questionTitle);
            qName.appendChild(qNameText);

            //QUESTION TYPE
            var qType = document.createElement("td");
            qType.setAttribute("class", "qType");
            var qTypeText = document.createTextNode(questionType);
            qType.appendChild(qTypeText);
            
            //ADD TO TABLE ROW
            qTableRow.appendChild(qInputContainer);
            qTableRow.appendChild(qName);
            qTableRow.appendChild(qType);
        }
    }

    function addPage () {
        var questionPages = document.getElementById("questionPages");
        var qPage = document.createElement("li");
        qPage.setAttribute("class", "qPage");
        
        if(pageCounter === 0){
            qPage.classList.add('selected');    
        }

        var qPageNumber = ++pageCounter;
        var qPageNumberNode = document.createTextNode(qPageNumber);
        
        qPage.onclick = function(){
            displayQuestions(qPageNumber)

            questionPages.childNodes.forEach(function(item){
                if(item.nodeName.toLowerCase() === 'li'){
                    item.classList.remove('selected');
                }
            });

            this.classList.add('selected');
        };
        qPage.appendChild(qPageNumberNode);
        questionPages.appendChild(qPage);
    }

    function displayQuestions (startQ, inc = questionsPerPage) {
        //CLEAR PAGE
        var parent = document.getElementById("questionTable");
        var child = document.getElementsByClassName("qTr");
        while (child.length > 0){
            parent.removeChild(child[0]);
        }
        
        //ADD QUESTIONS
        if (startQ == 0 || startQ == 1) {
            for (var i = 0; i < inc && i < questions.length; i++) {
                qArray[i].add(qArray[i].isChecked);
            }
        }
        else {
            for (var i = (startQ-1)*inc; i < (startQ*inc) && i < questions.length; i++) {
                qArray[i].add(qArray[i].isChecked);
            }
        }
    }

    function getChecked () {
        var wAlert = "Selected Question: "; 
        for (var i = 0; i < qArray.length; i++) {
            if (qArray[i].isChecked == true)
                wAlert += qArray[i].questionId.toString() + " ";
        }
        window.alert(wAlert)
    }

    //ADD QUESTIONS TO aArray
    for (var i = 0; i < questions.length; i++) {
        qArray[i] = new question(questions[i].id, questions[i].name, questions[i].type);
    }

    //DISPLAY INITIAL QUESTION PAGE
    displayQuestions(0);
    
    //ADD PAGES
    for (var i = 0; i < Math.ceil(qArray.length/questionsPerPage); i++) {
        addPage();
    }

    if(questions.length === 0){
        document.getElementById('noQWarning').style.display = 'block';
        document.getElementById('qList').style.display = 'none';
    }
    else{
        document.getElementById('noQWarning').style.display = 'none';
        document.getElementById('qList').style.display = 'block';   
    }
}

var bindContexts = function(){
    contexts.forEach(function(context){
        addCourse(context);
    });

    function addCourse (course) {
        var courseSelect = document.getElementById("courseSelect");
        
        var courseOption = document.createElement("option");
        courseOption.setAttribute("value", course.id);
        var courseText = document.createTextNode(course.name);
        courseOption.appendChild(courseText);
        
        courseSelect.appendChild(courseOption);
    }
};

var api = function(path, method, data, callback){
    YUI().use('io-xdr', function (Y) {
        var uri = path,
            json = data,
            cfg = {
                xdr: {
                  use : 'native'
                },
                method: method,
                data: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                },
                on: {

                    success: function (id, response) {
                        requestResponse = response.responseText;
                        callback(requestResponse);
                    }
                }
            };
        Y.io.header("X-Requested-With");
        var request = Y.io(uri, cfg);
    });
}

var sendChecked = function(){
    var payload = {
        title: document.getElementById('quizName').value,
        questions: []
    };

    for (var i = 0; i < qArray.length; i++) {
        if (qArray[i].isChecked == true)
            payload.questions.push(parseInt(qArray[i].questionId));
    }
    
    api('data.php/pdfgen', 'POST', payload, function(data){
        filename = 'test.pdf';

        var blob = new Blob([data], { type: 'application/pdf' });
        if (typeof window.navigator.msSaveBlob !== 'undefined') {
            // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
            window.navigator.msSaveBlob(blob, filename);
        } else {
            var URL = window.URL || window.webkitURL;
            var downloadUrl = URL.createObjectURL(blob);

            if (filename) {
                // use HTML5 a[download] attribute to specify filename
                var a = document.createElement("a");
                // safari doesn't support this yet
                if (typeof a.download === 'undefined') {
                    window.location = downloadUrl;
                } else {
                    a.href = downloadUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                }
            } else {
                window.location = downloadUrl;
            }

            setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
        }
    });
};

var init = function(){
    api('data.php/contexts', 'GET', null, function(data){
        contexts = JSON.parse(data).contexts;
        bindContexts();
    });

    document.getElementById('courseSelect').addEventListener('change', function(e){
        api('data.php/contexts/' + e.target.value, 'GET', null, function(data){
            questions = JSON.parse(data).questions;
            build();
        });
    });
}

init();
