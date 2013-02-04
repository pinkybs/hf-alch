package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	/**
	 * 包裹里的vo
	 * @author lite3
	 */
	public class ItemVo extends BasicVo
	{
		// cid
		public var cid:int;
		
		// id列表
		//public var ids:Array;
		public var id:String;
		//public function get id():String { return ids ? ids[0] : cid+""; }
		// 数量
		public var num:int;
		// 耐久度,仅装备有用
		public var wear:int;
		
		// 基类的引用
		public var base:BaseItemClassVo;
		
		
		
		// 基类的引用,弱类型
		public function get _base():* { return base; }
		
		override public function setData(obj:Object):BasicVo 
		{
			super.setData(obj);
			if (!id || "0" == id) id = cid + "";
			base = DataManager.getInstance().itemData.getItemClass(cid);
			return this;
		}
	}

}