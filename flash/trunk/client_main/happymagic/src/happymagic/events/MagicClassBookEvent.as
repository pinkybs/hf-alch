package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MagicClassBookEvent extends Event 
	{
		
		public static const SHOW_EVENT:String = "showMagicClassBook";
		public static const CLOSE_EVENT:String = "closeMagicClassBook";
		public function MagicClassBookEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new MagicClassBookEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("MagicClassBookEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}