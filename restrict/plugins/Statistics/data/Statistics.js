Mind.Plugins.Statistics= {
	tCount: 0,
	aCount: 0,
	/* needed methods */
	Run: function(){
		var tables= Mind.Project.knowledge.tables;
		
		this.tCount= 0;
		this.aCount= 0;
		this.lines= Mind.Project.knowledge.sentences.length;
//		for(var x in Mind.Project.knowledge)			Mind.try(function(){console.log(x+': '+Mind.Project.knowledge[x])});
//		alert();
		for(var t in tables)
		{
			this.tCount++;
			for(var a in tables[t].attributes)
				this.aCount++;
		}
	},
	Load: function(){
	},
	/*****************************/
	Build: function(){
		document.getElementById('Statistics_tCount').innerHTML= this.tCount;
		//Mind.Project.knowledge.processTime;
		document.getElementById('Statistics_aCount').innerHTML= this.aCount + " &nbsp; &nbsp; &nbsp; Average: "+ ((this.tCount>0)? ((((this.aCount/this.tCount))+'').substring(0, 6)): 0)+ '/table';
		document.getElementById('Statistics_sCount').innerHTML= this.lines;
		document.getElementById('Statistics_pTime').innerHTML= Mind.Project.knowledge.processTime + ' seconds';
	}
}
