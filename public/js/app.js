
pageCounter = 0;
questionsPerPage = 5;

var qArray = [];

var questions = [
    {
        id: 1,
        title: 'Въпрос №1',
        type: 'open',
        courses: [
            "course 1",
            "course 2",
            "course 3"
        ]
    },

    {
        id: 2,
        title: 'Въпрос №2',
        type: 'multiple',
        courses: [
            "course 2",
            "course 4"
        ]
    },

    {
        id: 3,
        title: 'Въпрос №3',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    },

    {
        id: 4,
        title: 'Въпрос №4',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    },

    {
        id: 5,
        title: 'Въпрос №4',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    },

    {
        id: 6,
        title: 'Въпрос №4',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    },

    {
        id: 7,
        title: 'Въпрос №4',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    },

    {
        id: 8,
        title: 'Въпрос №4',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    },

    {
        id: 9,
        title: 'Въпрос №4',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    },

    {
        id: 10,
        title: 'Въпрос №4',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    },

    {
        id: 11,
        title: 'Въпрос №4',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    },
    {
        id: 12,
        title: 'Въпрос №4',
        type: 'single',
        courses: [
            "course 1",
            "course 4"
        ]
    }
]

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

function addCourse (course) {
    var courseSelect = document.getElementById("courseSelect");
    
    var courseOption = document.createElement("option");
    courseOption.setAttribute("value", course);
    var courseText = document.createTextNode(course);
    courseOption.appendChild(courseText);
    
    courseSelect.appendChild(courseOption);
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
    qArray[i] = new question(questions[i].id, questions[i].title, questions[i].type);
}

//DISPLAY INITIAL QUESTION PAGE
displayQuestions(0);

//ADD PAGES
for (var i = 0; i < Math.ceil(qArray.length/questionsPerPage); i++) {
    addPage();
}