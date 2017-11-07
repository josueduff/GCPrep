/**
 * Interactive Pentagon
 * @constructor
 */
function SVGPentagon() {
    this.connections = {};
    this.adjacencyMatrix = {
        A: { A: 0, B: 0, C: 0, D: 0, E: 0 },
        B: { A: 0, B: 0, C: 0, D: 0, E: 0 },
        C: { A: 0, B: 0, C: 0, D: 0, E: 0 },
        D: { A: 0, B: 0, C: 0, D: 0, E: 0 },
        E: { A: 0, B: 0, C: 0, D: 0, E: 0 }        
    };
    this.container = document.getElementById("pentagon");
    this.nodes = this.container.getElementsByClassName("node");
    
    var Body = document.getElementsByTagName("body")[0];
    var vertexContainer = document.getElementById("vertex-container");
    var svgElementOffset = this.container.getBoundingClientRect();
    
    var dragOrigin;
    var dropTarget;
    var isDragging = false;
    var isSnappedToNode = false;
    var isComplete = false;

    var mouseX;
    var mouseY;

    var origin_X;
    var origin_Y;

    var vertex;

    console.log("Pentagon initiated");

    /**
     * Move the vertex.
     * Function is bound to "this" though the arrow function.
     * @param {e} event
     */
    var nodeDragHandler = function(e) {
        var dx = (e.clientX - origin_X) - svgElementOffset.left;
        var dy = (e.clientY - origin_Y) - svgElementOffset.top;
        var newD = 'm ' + origin_X + ',' + origin_Y + ' ' + dx + ',' + dy;

        vertex.setAttribute("d", newD);
    }
    
    
    function nodeStartDrag(e) {
        Body.style.webkitUserSelect = "none";
        Body.style.cursor = "-webkit-grabbing";
        isDragging = true;
        dragOrigin = e.target;
        dragOrigin.setAttribute("data-drag-origin", "");

        //Set the initial values of x and y, to the center of the dagged vertex's origin node.
        origin_X = dragOrigin.getAttribute("cx");
        origin_Y = dragOrigin.getAttribute("cy");

        var dx = e.clientX - origin_X;
        var dy = e.clientY - origin_Y;

        //Create a new vertex to drag.
        var newVertex = document.createElementNS("http://www.w3.org/2000/svg", "path");
        newVertex.setAttribute("class", "vertex");
        //newVertex.setAttribute("d", 'm ' + origin_X + ',' + origin_Y + ' ' + dx + ',' + dy);

        vertexContainer.appendChild(newVertex);

        //Set the current vertex to the previously created one.
        vertex = newVertex;

        for (var i = 0; i < this.nodes.length; i++) {
            if (Object.keys(this.connections).length == 0) { this.nodes[i].setAttribute("data-valid-drop", ""); }
            else {
                var name = dragOrigin.getAttribute("data-node-name") + this.nodes[i].getAttribute("data-node-name");

                if (this.connections.hasOwnProperty(name) || this.connections.hasOwnProperty(name[1] + name[0])) {
                    this.nodes[i].removeAttribute("data-valid-drop");
                } else {
                    this.nodes[i].setAttribute("data-valid-drop", "");
                }
            }
        }
        dragOrigin.removeAttribute("data-valid-drop");
        //Start the event listener to handle dragging
        this.container.addEventListener('mousemove', nodeDragHandler, false);

    }

     function snapActiveDrag(e) {
        Body.style.cursor = "pointer";
        if ((isDragging == true) && (e.target.hasAttribute("data-valid-drop"))) {
            isSnappedToNode = true;
            dropTarget = e.target;
            dropTarget.setAttribute("data-drop-target", "");
            dropTarget.setAttribute("r", "30.5");
                    
            var target_X = e.target.getAttribute("cx");
            var target_Y = e.target.getAttribute("cy");
            
            var hyp = Math.sqrt(Math.pow(target_Y - origin_Y, 2) + Math.pow(target_X - origin_X, 2));
            var cos = (target_X - origin_X) / hyp;
            var sin = (target_Y - origin_Y) / hyp;
        
            var dx = (hyp - 30) * cos;
            var dy = (hyp - 30) * sin;
            
            var newD = 'm'+ origin_X + ' ' + origin_Y + ' ' + dx + ' ' + dy;

            vertex.setAttribute("d", newD);
            this.container.removeEventListener('mousemove', nodeDragHandler, false);
        } else if ((isDragging == true) && (e.target == dragOrigin)){
            Body.style.cursor = "default";
        } else if (isDragging == true) {
            Body.style.cursor = "no-drop";
        }
    }

    function unSnapActiveDrag(e) {
        Body.style.cursor = "default";
        if ((isDragging == true) && (e.target.hasAttribute("data-valid-drop"))) {
            console.log("unsnap");
            isSnappedToNode = false;

            if (dropTarget != undefined) {
                dropTarget.removeAttribute("data-drop-target")
                dropTarget.setAttribute("r", "29");
            }

            var dx = e.clientX - origin_X;
            var dy = e.clientY - origin_Y;

            var newD = 'm ' + origin_X + ',' + origin_Y + ' ' + dx + ',' + dy;
            vertex.setAttribute("d", newD);

            this.container.addEventListener('mousemove', nodeDragHandler, false);
        }
        if (isDragging) {
            Body.style.cursor = "-webkit-grabbing";
        }
    }

    function releaseDrag() {
        Body.style.webkitUserSelect = "text";
        Body.style.cursor = "default";
        if (isDragging) {
            if (isSnappedToNode == false) {
                //Reset position if the vertex has not snapped to a node
                vertexContainer.removeChild(vertex);
            } else {
                //Save the connected objects
                this.adjacencyMatrix[dragOrigin.getAttribute("data-node-name")][dropTarget.getAttribute("data-node-name")] = 1;
                this.adjacencyMatrix[dropTarget.getAttribute("data-node-name")][dragOrigin.getAttribute("data-node-name")] = -1;
                
                this.connections[dragOrigin.getAttribute("data-node-name") + dropTarget.getAttribute("data-node-name")] = [dragOrigin, dropTarget, vertex];
                console.log("Connections", this.connections);
            }

            this.container.removeEventListener('mousemove', nodeDragHandler, false);

            for (var i = 0; i < this.nodes.length; i++) {
                this.nodes[i].removeAttribute("data-valid-drop");
                this.nodes[i].removeAttribute("data-drop-target");
            }

            //Reset
            dragOrigin.removeAttribute("data-drag-origin");
            dropTarget.setAttribute("r", "29");
            
            isDragging = false;
            isSnappedToNode = false;
            dragOrigin = undefined;
            dropTarget = undefined;
            vertex = undefined;
        }
    }
    
    
    //Initiate Events            

    this.container.addEventListener('mousemove', function(e) { mouseX = e.clientX, mouseY = e.clientY; }, 'false');
    this.container.addEventListener('mouseup', releaseDrag.bind(this), 'false');
    
    //Add event listeners for all the nodes
    for (var i = 0; i < this.nodes.length; i++) {
        this.nodes[i].addEventListener('mousedown', nodeStartDrag.bind(this), false);
        this.nodes[i].addEventListener('mouseover', snapActiveDrag.bind(this), false);
        this.nodes[i].addEventListener('mouseout', unSnapActiveDrag.bind(this), false);
    }
}
    
/**
 * Undo the last connection made.
 */
SVGPentagon.prototype.undo = function() {
    if (Object.keys(this.connections).length > 0) {
        var lastConnectionName = Object.keys(this.connections).pop();
        var lastConnection = this.connections[lastConnectionName];
        var vertex = lastConnection[2];

        console.log("Connection " + lastConnectionName[0] + "-" + lastConnectionName[1] + " broken.");

        isComplete = false;

        vertexContainer.removeChild(vertex);
        delete this.connections[lastConnectionName];
    } else {
        console.error("Nothing to Undo");
    }
}

SVGPentagon.prototype.trace = function(from, to) {
    if (Object.keys(this.connections).length > 0) {
        var from = from.toUpperCase();
        var to = to.toUpperCase();

        var keys = Object.keys(this.connections); 

        var pathStarts = [];
        var pathEnds = [];
        for (var i = 0; i < keys.length; i++) {
            if (keys[i][0] == from) {
                pathStarts.push(keys[i]);
            }
            if (keys[i][1] == to) {
                pathEnds.push(keys[i]);
            }
        }                

        var path = [];
        for (var i = 0; i < pathStarts.length; i++) {
            if ((pathStarts[i][0] == from) && (pathStarts[i][1] == to)) {
                path = [pathStarts[i]];
                break;
            }
            for (var j = 0; j < keys.length; j++) {
                if (keys[j][0] == pathStarts[i][1]) {
                    if (keys[j][1] == to) {
                        path = [pathStarts[i], keys[j]];
                        break;
                    }
                    for (var k = 0; k < keys.length; k++) {
                        if (keys[k][0] == keys[j][1]) {
                            if (keys[k][1] == to) {
                                path = [pathStarts[i], keys[j], keys[k]];
                                break;
                            }
                            for (var l = 0; l < keys.length; l++) {
                                if (keys[l][0] == keys[k][1]) {
                                    if (keys[l][1] == to) {
                                        path = [pathStarts[i], keys[j], keys[k], keys[l]];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (path.length > 0) {
            var nodeCenters = {
                "A": { X: 149, Y: 49},
                "B": { X: 257, Y: 126},
                "C": { X: 215, Y: 251},
                "D": { X: 84, Y: 251},
                "E": { X: 43, Y: 126}
            }

            var nodeTracer = document.getElementsByClassName("node-tracer")[0];
            var keyIndex = 0;
            var lastPosition;
            var isTracing = false;

            nodeTracer.setAttribute("data-tracing", "false");

            nodeTracer.setAttribute("cx", nodeCenters[from].X);
            nodeTracer.setAttribute("cy", nodeCenters[from].Y);

            nodeTracer.setAttribute("data-tracing", "true");
            isTracing = true;
            //Perform the first translation
            nodeTracer.style.transform = "translate(" +
                (nodeCenters[path[keyIndex][1]].X - nodeCenters[from].X) + "px, " +
                (nodeCenters[path[keyIndex][1]].Y - nodeCenters[from].Y) + "px)";

            nodeTracer.addEventListener('transitionend', function(e) {
                if (keyIndex < path.length) {
                    var previousLocation = nodeTracer.style.transform;
                    previousLocation = previousLocation.replace(/translate\(/, '').replace(/px/g, '').split(',');
                    previousLocation.forEach(function(curr, index, array) { array[index] = parseFloat(curr); });

                    var transformation = "translate(" + (nodeCenters[path[keyIndex][1]].X - nodeCenters[path[keyIndex][0]].X) + "px, " + (nodeCenters[path[keyIndex][1]].Y - nodeCenters[path[keyIndex][0]].Y) + "px)";

                    nodeTracer.style.transform = transformation;
                    console.log(transformation);

                    lastPosition = path[keyIndex][1];
                    keyIndex++; 
                } else {
                    e.srcElement.removeEventListener(e.type, arguments.callee);
                    nodeTracer.setAttribute("data-tracing", "done");
                }
            }, false);

        }
        else {
            console.error("The chosen path is not traceable");
        }
    } else {
        console.error("No connections to trace");
    }

}
