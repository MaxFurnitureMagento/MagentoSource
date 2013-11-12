if (!Itoris) {
	var Itoris = {};
}

Itoris.Sticker = Class.create({
	angleWidth: 20,
	activeTitleIncrease: 3,
	contentBorderWidth: 2,
	stickers : [],
	loading : null,
	orientations : ['top', 'right', 'bottom', 'left'],
	bindToElementIds : [],
	isIE7: false,
	isIE8: false,
	isIE: false,
	styles : {
		red : [
			{type: 'color', value: '#ffffff'},
			{type: 'background-color', value: '#e20202'},
			{type: 'border', value: '2px solid #cd0000'}
		],
		black : [
			{type: 'color', value: '#ffb400'},
			{type: 'background-color', value: '#565656'},
			{type: 'border', value: '2px solid #ffb400'}
		],
		orange : [
			{type: 'color', value: '#ffb400'},
			{type: 'background-color', value: '#565656'},
			{type: 'border', value: '2px solid #ffb400'}
		],
		white : [
			{type: 'color', value: '#000000'},
			{type: 'background-color', value: '#e5e5e5'},
			{type: 'border', value: '2px solid #ff0000'}
		],
		gold : [
			{type: 'color', value: '#000000'},
			{type: 'background-color', value: '#fac757'},
			{type: 'border', value: '2px solid #feae02'}
		],
		red_white : [
			{type: 'color', value: '#000000'},
			{type: 'background-color', value: '#ffffff'},
			{type: 'border', value: '2px solid #cd0000'}
		],
		green : [
			{type: 'color', value: '#000000'},
			{type: 'background-color', value: '#61a30e'},
			{type: 'border', value: '2px solid #72b61e'}
		],
		blue : [
			{type: 'color', value: '#000000'},
			{type: 'background-color', value: '#ffffff'},
			{type: 'border', value: '2px solid #ffffff'}
		]
	},
	initialize: function(stickerId, theme, orientation, bindToElementId, config) {
		if (Prototype.Browser.IE) {
			Itoris.Sticker.prototype.isIE = true;
			var ieVersion = parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5));
			Itoris.Sticker.prototype.isIE7 = ieVersion == 7;
			Itoris.Sticker.prototype.isIE8 = ieVersion == 8;
		}
		this.content = 	$('itoris_sticker_content_' + stickerId);
		this.sticker = $('itoris_sticker_' + stickerId);
		this.bindToElement = bindToElementId ? $(bindToElementId) : null;
		this.config = config;
		if (bindToElementId && !this.bindToElement) {
			this.content.remove();
			this.sticker.remove();
			return;
		}
		this.id = stickerId;
		this.bindToElementId = bindToElementId;
		if (bindToElementId) {
			this.bindToElementIds.push(bindToElementId);
		}
		this.theme = theme;
		this.orientation = orientation;
		Event.observe(this.sticker, 'click', this.toogleStickerContent.bind(this));
		this.stickers.push(this);
	},
	prepareStickers: function() {
		if (!this.loading) {
			this.loading = new PeriodicalExecuter(this.prepareStickers.bind(this), 0.1);
		}
		if (this.isAllStickersLoaded()) {
			this.loading.stop();
			this.showStickers();
		}
	},
	showStickers : function() {
		for (var i = 0; i < this.orientations.length; i++) {
			for (var j = -1; j < this.bindToElementIds.length; j++) {
				var bindToElementId = j == -1 ? null : this.bindToElementIds[j];
				var stickers = this.getStickers(this.orientations[i], bindToElementId);
				if (stickers.length) {
					var expandedSticker = null;
					for (var z = 0; z < stickers.length; z++) {
						stickers[z].sticker.show();
						if (stickers[z].config.expand) {
							expandedSticker = stickers[z];
						}
					}
					this.setStickersPosition(stickers, this.orientations[i]);
					if (expandedSticker) {
						expandedSticker.toogleStickerContent();
					}
				}
			}
		}
		//$$('.itoris_sticker').each(function(elm) {elm.absolutize();});
	},
	getStickers: function(orientation, bindToElementId) {
		var stickers = [];
		for (var i = 0; i < this.stickers.length; i++) {
			if (this.stickers[i].orientation == orientation && ((!bindToElementId && !this.stickers[i].bindToElementId) || this.stickers[i].bindToElementId == bindToElementId)) {
				stickers.push(this.stickers[i]);
			}
		}
		return stickers;
	},
	isStickerLoaded: function() {
		return this.sticker.isLoaded;
	},
	isAllStickersLoaded : function() {
		for (var i = 0; i < this.stickers.length; i++) {
			if (!this.stickers[i].isStickerLoaded()) {
				return false;
			}
		}
		return true;
	},
	toogleStickerContent: function() {
		var sticker = this.getOtherOpenedSticker();
		if (sticker) {
			return this.replaceSticker(sticker);
		} else {
			return this.content.visible() ? this.hideStickerContent() : this.showStickerContent();
		}
	},
	getOtherOpenedSticker : function() {
		var stickers = this.getStickers(this.orientation, this.bindToElementId);
		for (var i = 0; i < stickers.length; i++) {
			if (stickers[i].id != this.id && stickers[i].content.isOpen) {
				return stickers[i];
			}
		}
		return null;

	},
	getStyles : function(colorScheme, elm) {
		var styles = this.styles[colorScheme];
		var stylesStr = '';
		var obj = {};
		for (var i = 0; i < styles.length; i++) {
			if (typeof styles[i].type != 'undefined') {
				stylesStr += styles[i].type + ':' + styles[i].value + ';';
				if (elm) {
					obj[styles[i].type.camelize()] = styles[i].value;
				}
			}
		}
		if (elm) {
			elm.setStyle(obj);
		}
		return stylesStr;
	},
	replaceSticker: function(sticker) {
		var origContent = this.content;
		var origWidth = sticker.content.getWidth();
		var origOverflow = sticker.content.getStyle('overflow');
		sticker.stickersContentBox.setStyle({overflow: 'hidden'});
		this.getStyles(this.config.color_scheme, sticker.stickersContentBox);
		if (this.isHorizontalOrientation()) {
			var margins = 'margin-top:' + ((this.isIE7 ? -this.stickersTitleBox.getHeight()/2 : 0) -this.contentBorderWidth - this.content.origStyles.height / 2) + 'px;';
		} else {
			var margins = 'margin-left:' + (-this.contentBorderWidth - this.content.origStyles.width / 2) + 'px;';
		}

		new Effect.Morph(sticker.stickersContentBox, {
			style: 'width: '
				+ this.content.origStyles.width +'px;height:' + this.content.origStyles.height + 'px;'
				+ margins,
			duration: 0.5,
			afterFinishInternal : function(effect) {
				sticker.stickersContentBox.setStyle({'overflow' : 'visible'});
			}
		});
		origContent.show();
		sticker.content.hide();
		if (this.isIE7 && this.isHorizontalOrientation()) {
			new Effect.Morph(sticker.stickersBox, {
					style: 'width: ' + (this.content.origStyles.width) +'px;',
				duration: 0.5
			});
		}

		var stickerChangeType = this.isHorizontalOrientation() ? 'width' : 'height';
		this.changeStickerWidthHeight(this, stickerChangeType, true, false);
		this.changeStickerWidthHeight(sticker, stickerChangeType, false, false);
		sticker.content.isOpen = false;
		this.content.isOpen = true;
	},
	showStickerContent: function(useAppearEffect) {
		this.closeOtherStickers();

		if (!useAppearEffect) {
			this.content.show();
		}
		this.content.isOpen = true;
		this.content.setStyle({zIndex: this.isHorizontalOrientation() ? '10012' : '10010'});
		this.getStyles(this.config.color_scheme, this.stickersContentBox);
		var stickersContentBoxStyles = {
			width: this.content.origStyles.width + 'px',
			height: this.content.origStyles.height + 'px'
		};
		if (this.isHorizontalOrientation()) {
			stickersContentBoxStyles.marginTop = ((this.isIE7 ? -this.stickersTitleBox.getHeight()/2 : 0)
				- this.contentBorderWidth - this.content.origStyles.height / 2) + 'px';
		} else {
			stickersContentBoxStyles.marginLeft = (-this.contentBorderWidth-this.content.origStyles.width / 2) + 'px';
		}
		this.stickersContentBox.setStyle(stickersContentBoxStyles);
		this.sticker.setStyle({zIndex: this.isHorizontalOrientation() ? '10013' : '10011'});
		var contentWidth = this.content.getWidth();
		var contentHeight = this.content.getWidth();
		if (!useAppearEffect) {
			this.stickersBox.select('.itoris-stickers-content-box')[0].setStyle({
				width: (this.isIE7 && this.isHorizontalOrientation() ? this.content.getWidth() : this.content.origStyles.width)  + 'px',
				height: this.content.origStyles.height + 'px'
			})
		}
		var stickerBoxStyles = {};
		if (this.isHorizontalOrientation()) {
			stickerBoxStyles[this.orientation] = -this.content.getWidth() + 'px';
			stickerBoxStyles.width = this.isIE7 ? this.content.getWidth() : 'auto';
		} else {
			stickerBoxStyles[this.orientation] = -this.content.getHeight() + 'px';
			stickerBoxStyles.width = '100%';
		}
		this.stickersBox.setStyle(stickerBoxStyles);
		if (this.isHorizontalOrientation()) {
			this.slideStickerContent(this.orientation, contentWidth, 0);
			this.changeStickerWidthHeight(this, 'width', true, true);
		} else {
			this.slideStickerContent(this.orientation, contentHeight, 0);
			this.changeStickerWidthHeight(this, 'height', true, true);
		}
	},
	changeStickerWidthHeight : function(obj, type, increase, moveBox) {
		if (obj.getStickers(obj.orientation, obj.bindToElementId).length > 1) {
			if (moveBox) {
				obj.sticker.up().select('.itoris_sticker').each(function(elm) {
					if (elm != obj.sticker) {
						obj.changeStickerVisibility(elm, true);
					}
					if (!increase && elm.hasClassName('itoris_sticker_notactive')) {
						obj.changeStickerVisibility(elm, false);
					}
				});
			}
			if (increase) {
				if (obj.sticker.hasClassName('itoris_sticker_notactive')) {
					obj.changeStickerVisibility(obj.sticker, false);
				}
			} else if (!moveBox) {
				if (!obj.sticker.hasClassName('itoris_sticker_notactive')) {
					obj.changeStickerVisibility(obj.sticker, true);
				}
			}
		}
	},
	changeStickerVisibility : function(elm, hide) {
		if (hide) {
			if (!elm.hasClassName('itoris_sticker_notactive')) {
				elm.addClassName('itoris_sticker_notactive');
			}
		} else {
			if (elm.hasClassName('itoris_sticker_notactive')) {
				elm.removeClassName('itoris_sticker_notactive');
			}
		}
	},
	hideStickerContent: function() {
		if (this.isHorizontalOrientation()) {
			this.slideStickerContent(this.orientation, 0, -this.content.getWidth() - this.contentBorderWidth * 2);
			this.changeStickerWidthHeight(this, 'width', false, true);
		} else {
			this.slideStickerContent(this.orientation, 0, -this.content.getHeight() - this.contentBorderWidth * 2);
			this.changeStickerWidthHeight(this, 'height', false, true);
		}
		this.content.isOpen = false;
		setTimeout(this.hideContent.bind(this), 800)
	},
	closeOtherStickers: function() {
		for (var i = 0; i < this.stickers.length; i++) {
			if (this.stickers[i].id != this.id
				&& this.stickers[i].content.isOpen
				&& (
					this.stickers[i].orientation != this.orientation
					|| this.stickers[i].bindToElementId != this.bindToElementId
				)
			) {
				this.stickers[i].hideStickerContent();
			}
		}
	},
	closeStickers : function(e) {
		var elm = e.target || e.srcElement;
		Element.extend(elm);
		if (typeof elm.descendantOf == 'function') {
			var classes = ['.itoris-stickers-content-box', '.itoris-stickers-title-box'];
			for (var i = 0; i < classes.length; i++) {
				var boxes = $$(classes[i]);
				for (var j = 0; j < boxes.length; j++) {
					if (elm.descendantOf(boxes[j])) {
						return true;
					}
				}
			}
		}
		this.closeOtherStickers();
	},
	slideStickerContent: function(direction, contentWidth, contentPosition) {
		new Effect.Morph(this.stickersBox, {
			style: direction + ':' + contentPosition + 'px;',
			duration: 0.5
		});
	},
	hideContent: function() {
		this.content.hide();
		this.content.setStyle({zIndex: '10000'});
		this.sticker.setStyle({zIndex: '10001'});
	},
	isHorizontalOrientation : function() {
		return this.orientation == 'left' || this.orientation == 'right';
	},
	correctContentViewport: function(block, borderWidth) {
		var isHorizontalOrientation = this.orientation == 'left' || this.orientation == 'right';
		this.overflow = false;
		var stickerCorrection = isHorizontalOrientation ? this.sticker.getWidth() : this.sticker.getHeight();
		var paddingLeft = block.id ? parseInt(block.getStyle('paddingLeft')) : 0;
		var paddingRight = block.id ? parseInt(block.getStyle('paddingRight')) : 0;
		var maxWidth = block.getWidth() - paddingLeft - paddingRight - borderWidth - (isHorizontalOrientation ? stickerCorrection : 0);
		if (this.content.getWidth() > maxWidth) {
			this.content.setStyle({width: maxWidth + 'px'});
			this.overflow = true;
		}
		var paddingTop = block.id ? parseInt(block.getStyle('paddingTop')) : 0;
		var paddingBottom = block.id ? parseInt(block.getStyle('paddingBottom')) : 0;
		var maxHeight = block.getHeight() - paddingTop - paddingBottom - borderWidth - (isHorizontalOrientation ? 0 : stickerCorrection);
		if (this.content.getHeight() > maxHeight) {
			this.content.setStyle({height: maxHeight + 'px'});
			this.overflow = true;
		}
		if (this.overflow) {
			this.content.setStyle({overflow: 'scroll'});
		}
	},
	createStickersBox: function(orientation) {
		var box = document.createElement('div');
		Element.extend(box);
		box.addClassName('itoris-stickers-box');
		var contentBox = document.createElement('div');
		Element.extend(contentBox);
		contentBox.addClassName('itoris-stickers-content-box');

		var titleBox = document.createElement('div');
		Element.extend(titleBox);
		titleBox.addClassName('itoris-stickers-title-box');
		switch (orientation) {
			case 'left':
			case 'right':
				box.appendChild(titleBox);
				box.appendChild(contentBox);
				contentBox.setStyle({position: 'relative', top: '50%'});
				break;
			case 'top':
				box.appendChild(contentBox);
				box.appendChild(titleBox);
				contentBox.setStyle({position: 'relative', left: this.isIE7 ? 0 : '50%'});
				break;
			case 'bottom':
				box.appendChild(titleBox);
				box.appendChild(contentBox);
				contentBox.setStyle({position: 'relative', left: this.isIE7 ? 0 : '50%'});
				break;
		}

		return box;
	},
	setStickersPosition: function(stickers, orientation) {
		var borderWidth = this.contentBorderWidth;
		var totalWidth = 0;
		var totalHeight = 0;
		var stickersBox = this.createStickersBox(orientation);
		var stickersContentBox = stickersBox.select('.itoris-stickers-content-box')[0];
		var stickersTitleBox = stickersBox.select('.itoris-stickers-title-box')[0];
		var isBinded = false;

		var maxStickerHeight = 0;
		var maxStickerWidth = 0;
		for (var i = 0; i < stickers.length; i++) {
			totalWidth += stickers[i].sticker.getWidth();
			totalHeight += stickers[i].sticker.getHeight();
			isBinded = stickers[i].bindToElementId;
			if (stickers[i].isHorizontalOrientation()) {
				if (maxStickerWidth < stickers[i].sticker.getWidth()) {
					maxStickerWidth = stickers[i].sticker.getWidth();
				}
			} else {
				if (maxStickerHeight < stickers[i].sticker.getHeight()) {
					maxStickerHeight = stickers[i].sticker.getHeight();
				}
			}
		}

		stickersBox.setStyle({position: isBinded ? 'absolute' : 'fixed'});
		if (isBinded) {
			var bindToElement = stickers[0].bindToElement;
			bindToElement.setStyle({position: 'relative', overflow: 'hidden'});
			bindToElement.appendChild(stickersBox);
		} else {
			$$('body')[0].appendChild(stickersBox);
		}
		var currentWidth = 0;
		var currentHeight = 0;
		for (var i = 0; i < stickers.length; i++) {
			var stickerObj = stickers[i];
			stickerObj.stickersBox = stickersBox;
			stickerObj.stickersContentBox = stickersContentBox;
			stickerObj.stickersTitleBox = stickersTitleBox;
			stickerObj.sticker.setStyle({float: 'left'});
			stickerObj.content.setStyle({position: 'absolute'});
			if (!stickerObj.bindToElement) {
				stickerObj.correctContentViewport(document.viewport, borderWidth * 2);
			} else {
				stickerObj.correctContentViewport(stickerObj.bindToElement, borderWidth * 2);
			}
			var contentPosition = {};
			var stickerWidth = stickerObj.sticker.getWidth();
			var stickerHeight = stickerObj.sticker.getHeight();
			var contentHeight = stickerObj.content.getHeight();
			var contentWidth = stickerObj.content.getWidth();
			var angleWidth = stickerObj.angleWidth;
			var smallContentBlock = false;

			if (contentHeight < totalHeight && stickerObj.isHorizontalOrientation()) {
				contentPosition.height = (totalHeight - borderWidth * 2 - ((stickerObj.theme == 'oblique') ? (angleWidth -  borderWidth * 2) : 0)) + 'px';
				contentHeight = totalHeight;
				smallContentBlock = true;
			}
			if (contentWidth < totalWidth  && !stickerObj.isHorizontalOrientation()) {
				contentPosition.width = (totalWidth - borderWidth * 2 - ((stickerObj.theme == 'oblique') ? (angleWidth -  borderWidth * 2) : 0)) + 'px';
				contentWidth = totalWidth;
				smallContentBlock = true;
			}


			switch (orientation) {
				case 'top':
					contentPosition.top = 0;
					contentPosition.left = 0;//stickerObj.isIE7 ? 0 : '50%';
					contentPosition.marginLeft = 0;//this.isIE7 ? 0 : '-' + (contentWidth/2  + ((this.theme == 'oblique' && smallContentBlock) ? -(angleWidth -  borderWidth * 2) : 0)) + 'px';
					stickersTitleBox.setStyle({
						position: 'relative',
						left: stickerObj.isIE7 ? 0 : '50%',
						top: 0,
						marginLeft: - totalWidth / 2 + 'px',
						width: totalWidth + 'px',
						height: maxStickerHeight + 'px'
					});
					stickersBox.setStyle({left: 0, top: 0, height: 'auto', width:'100%'});
					break;
				case 'left':
					contentPosition.left = 0;
					contentPosition.top = 0;//this.isIE7 ? 0 : '50%';
					contentPosition.marginTop = 0;//'-' + (contentHeight/2) + 'px';
					stickersTitleBox.setStyle({
						position: 'relative',
						float: 'right',
						marginRight: -maxStickerWidth + 'px',
						top:'50%',
						marginTop: -totalHeight / 2 + 'px',
						width:maxStickerWidth + 'px'
					});
					stickersBox.setStyle({left: 0, top: 0, height: '100%', width:0});
					break;
				case 'right':
					contentPosition.right = 0;
					contentPosition.top = 0;//this.isIE7 ? 0 : '50%';
					contentPosition.marginTop = 0;//'-' + (contentHeight/2 + ((this.theme == 'oblique' && smallContentBlock) ? -(angleWidth -  borderWidth * 2) : 0)) + 'px';
					stickersTitleBox.setStyle({
						position: 'relative',
						top: '50%',
						marginLeft: -maxStickerWidth + 'px',
						float: 'left',
						marginTop: - totalHeight / 2 + 'px',
						width: maxStickerWidth  + 'px',
						height: totalHeight + 'px'
					});
					stickersBox.setStyle({right: 0, top: 0, height: '100%', width:0})
					if (maxStickerWidth > stickerObj.sticker.getWidth()) {
						stickerObj.sticker.setStyle({marginLeft: (maxStickerWidth - stickerObj.sticker.getWidth()) + 'px'});
					}
					break;
				case 'bottom':
					contentPosition.bottom = 0;
					contentPosition.left = 0;//stickerObj.isIE7 ? 0 : '50%';
					contentPosition.marginLeft = 0;//this.isIE7 ? 0 : '-' + (contentWidth/2) + 'px';
					stickersTitleBox.setStyle({
						position: 'relative',
						left: stickerObj.isIE7 ? 0 : '50%',
						bottom: 0,
						marginLeft: - totalWidth / 2 + 'px',
						width: totalWidth  + 'px',
						height: maxStickerHeight + 'px'
					});
					stickersBox.setStyle({left: 0, bottom: 0, height: 'auto', width:'100%'})
					if (maxStickerHeight > stickerObj.sticker.getHeight()) {
						stickerObj.sticker.setStyle({marginTop: (maxStickerHeight - stickerObj.sticker.getHeight()) + 'px'});
					}
					break;
			}
			stickerObj.content.setStyle(contentPosition);
			currentWidth += stickerWidth;
			currentHeight += stickerHeight;
			stickersTitleBox.appendChild(stickerObj.sticker);
			stickerObj.sticker.origStyles = {
				marginTop: parseNumber(stickerObj.sticker.getStyle('marginTop')) ? parseNumber(stickerObj.sticker.getStyle('marginTop')) : 0,
				marginLeft:parseNumber(stickerObj.sticker.getStyle('marginLeft')) ? parseNumber(stickerObj.sticker.getStyle('marginLeft')) : 0,
				height: stickerObj.sticker.getHeight(),
				width: stickerObj.sticker.getWidth()
			};
			stickerObj.sticker.style.height = stickerObj.sticker.getHeight() + 'px';
			stickerObj.sticker.style.width = stickerObj.sticker.getWidth() + 'px';
			stickerObj.sticker.addClassName('itoris-sticker-orientation-' + orientation);

			stickerObj.content.origStyles = {
				color: stickerObj.content.getStyle('color'),
				marginTop:stickerObj.content.getStyle('marginTop'),
				marginLeft:stickerObj.content.getStyle('marginLeft'),
				backgroundColor: stickerObj.content.getStyle('backgroundColor'),
				width: stickerObj.content.getWidth(),
				height: stickerObj.content.getHeight()
			}
		}
		for (var i = 0; i < stickers.length; i++) {
			var stickerObj = stickers[i];
			stickersContentBox.appendChild(stickerObj.content);
		}
	}
});