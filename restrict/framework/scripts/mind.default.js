$(document).ready(function(){
    //$.getScript("framework/scripts/mind.ui/mind.dialog.js");
    Mind.try= function(f, c, fn)
    {
    	var ret= false;
    	try
    	{
    		f();
    		ret= true;
    	}catch(e)
    	{
    		if(c)
    			c(e);
    	}finally{
    		if(fn)
    			fn();
    	}
    	if(ret)
    		return true;
    	else
    		return false;
    }
});
