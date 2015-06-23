<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

final class Net
{
	const IP_PATTERN = '([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])';

	// See http://en.wikipedia.org/wiki/Domain_Name_System#Domain_name_syntax
	// Syntax rules:
	//  1. max 127 labels
	//  2. label length up to 63 chars
	//  3. domain name length up to 253 chars
	//  4. domain name: [a-z0-9-], no hyphen at end or start

	// original regex from http://stackoverflow.com/questions/1418423/the-hostname-regex
	const HOST_PATTERN_RE = '%^(?=.{1,255}$)[0-9a-z](?:(?:[0-9a-z-_]|\b-){0,61}[0-9a-z-_])?(?:\.[0-9a-z](?:(?:[0-9a-z-_]|\b-){0,61}(?:xn--)?[0-9a-z])?)*\.?$%';

	// simple regex that match host syntax
	const HOST_PATTERN_RELAXED_FLEXIBLE = '(?P<host>(?:[a-z0-9_-]+\.)+[a-z]+)';
	const HOST_PATTERN_RELAXED_STRICT = '(?P<host>(?:[a-z0-9_-]+\.)*[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?\.(?:(?:[a-z]+\.)*[a-z]+))';

	// regex that checks label length
	const HOST_PATTERN_HARDENED_RE = '!^(?=.{1,253}$)(?:(?=[a-z0-9_-]{1,63}\.)(?:[a-z0-9_-]+\.))*(?:(?=[a-z0-9_-]{1,63}\.)[a-z0-9](?:[a-z0-9_-]*[a-z0-9])?\.)(?:(?:[a-z]+\.)*[a-z]+)$!';

	public static $roots = array(
		'ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar',
		'as', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg',
		'bh', 'bi', 'bj', 'bl', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bu', 'bv',
		'bw', 'by', 'bz', 'ca', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl',
		'cm', 'cn', 'co', 'cr', 'cs', 'cu', 'cv', 'cx', 'cy', 'cz', 'dd', 'de',
		'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'ee', 'eg', 'eh', 'er', 'es', 'et',
		'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf',
		'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu',
		'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im',
		'in', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jp', 'ke', 'kg',
		'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc',
		'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me',
		'mf', 'mg', 'mh', 'mk', 'ml', 'mm', 'mn', 'mo', 'mp', 'mq', 'mr', 'ms',
		'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nc', 'ne', 'nf', 'ng',
		'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'pa', 'pe', 'pf', 'pg',
		'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'ps', 'pt', 'pw', 'py', 'qa', 're',
		'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si',
		'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz',
		'tc', 'td', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp',
		'tr', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'um', 'us', 'uy', 'uz',
		'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'ye', 'yt', 'yu',
		'za', 'zm', 'zr', 'zw',
		'biz', 'cat', 'com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'pro',
		'tel', 'xxx', 'aero', 'arpa', 'asia', 'coop', 'info', 'jobs', 'mobi',
		'name', 'nato', 'post', 'museum', 'travel',
	);

	public static $validTlds = array(
		'ab.ca', 'bc.ca', 'mb.ca', 'nb.ca', 'nf.ca', 'nl.ca', 'ns.ca', 'nt.ca', 'nu.ca',
		'pe.ca', 'qc.ca', 'sk.ca', 'yk.ca', 'com.cd', 'net.cd', 'org.cd', 'com.ch',
		'org.ch', 'gov.ch', 'co.ck', 'ac.cn', 'com.cn', 'edu.cn', 'gov.cn', 'net.cn',
		'ah.cn', 'bj.cn', 'cq.cn', 'fj.cn', 'gd.cn', 'gs.cn', 'gz.cn', 'gx.cn', 'ha.cn',
		'he.cn', 'hi.cn', 'hl.cn', 'hn.cn', 'jl.cn', 'js.cn', 'jx.cn', 'ln.cn', 'nm.cn',
		'qh.cn', 'sc.cn', 'sd.cn', 'sh.cn', 'sn.cn', 'sx.cn', 'tj.cn', 'xj.cn', 'xz.cn',
		'zj.cn', 'com.co', 'edu.co', 'org.co', 'gov.co', 'mil.co', 'net.co', 'nom.co',
		'edu.cu', 'org.cu', 'net.cu', 'gov.cu', 'inf.cu', 'gov.cx', 'edu.do', 'gov.do',
		'com.do', 'org.do', 'sld.do', 'web.do', 'net.do', 'mil.do', 'art.do', 'com.dz',
		'net.dz', 'gov.dz', 'edu.dz', 'asso.dz', 'pol.dz', 'art.dz', 'com.ec', 'info.ec',
		'fin.ec', 'med.ec', 'pro.ec', 'org.ec', 'edu.ec', 'gov.ec', 'mil.ec', 'com.ee',
		'fie.ee', 'pri.ee', 'eun.eg', 'edu.eg', 'sci.eg', 'gov.eg', 'com.eg', 'org.eg',
		'mil.eg', 'com.es', 'nom.es', 'org.es', 'gob.es', 'edu.es', 'com.et', 'gov.et',
		'edu.et', 'net.et', 'biz.et', 'name.et', 'info.et', 'co.fk', 'org.fk', 'gov.fk',
		'nom.fk', 'net.fk', 'tm.fr', 'asso.fr', 'nom.fr', 'prd.fr', 'presse.fr',
		'gouv.fr', 'com.ge', 'edu.ge', 'gov.ge', 'org.ge', 'mil.ge', 'net.ge', 'pvt.ge',
		'net.gg', 'org.gg', 'com.gi', 'ltd.gi', 'gov.gi', 'mod.gi', 'edu.gi', 'org.gi',
		'ac.gn', 'gov.gn', 'org.gn', 'net.gn', 'com.gr', 'edu.gr', 'net.gr', 'org.gr',
		'com.hk', 'edu.hk', 'gov.hk', 'idv.hk', 'net.hk', 'org.hk', 'com.hn', 'edu.hn',
		'net.hn', 'mil.hn', 'gob.hn', 'iz.hr', 'from.hr', 'name.hr', 'com.hr', 'com.ht',
		'firm.ht', 'shop.ht', 'info.ht', 'pro.ht', 'adult.ht', 'org.ht', 'art.ht',
		'rel.ht', 'asso.ht', 'perso.ht', 'coop.ht', 'med.ht', 'edu.ht', 'gouv.ht',
		'co.in', 'firm.in', 'net.in', 'org.in', 'gen.in', 'ind.in', 'nic.in', 'ac.in',
		'res.in', 'gov.in', 'mil.in', 'ac.ir', 'co.ir', 'gov.ir', 'net.ir', 'org.ir',
		'gov.it', 'co.je', 'net.je', 'org.je', 'edu.jm', 'gov.jm', 'com.jm', 'net.jm',
		'org.jo', 'net.jo', 'edu.jo', 'gov.jo', 'mil.jo', 'co.kr', 'or.kr', 'com.kw',
		'gov.kw', 'net.kw', 'org.kw', 'mil.kw', 'edu.ky', 'gov.ky', 'com.ky', 'org.ky',
		'org.kz', 'edu.kz', 'net.kz', 'gov.kz', 'mil.kz', 'com.kz', 'com.li', 'net.li',
		'gov.li', 'gov.lk', 'sch.lk', 'net.lk', 'int.lk', 'com.lk', 'org.lk', 'edu.lk',
		'soc.lk', 'web.lk', 'ltd.lk', 'assn.lk', 'grp.lk', 'hotel.lk', 'com.lr',
		'gov.lr', 'org.lr', 'net.lr', 'org.ls', 'co.ls', 'gov.lt', 'mil.lt', 'gov.lu',
		'org.lu', 'net.lu', 'com.lv', 'edu.lv', 'gov.lv', 'org.lv', 'mil.lv', 'id.lv',
		'asn.lv', 'conf.lv', 'com.ly', 'net.ly', 'gov.ly', 'plc.ly', 'edu.ly', 'sch.ly',
		'org.ly', 'id.ly', 'co.ma', 'net.ma', 'gov.ma', 'org.ma', 'tm.mc', 'asso.mc',
		'nom.mg', 'gov.mg', 'prd.mg', 'tm.mg', 'com.mg', 'edu.mg', 'mil.mg', 'com.mk',
		'com.mo', 'net.mo', 'org.mo', 'edu.mo', 'gov.mo', 'org.mt', 'com.mt', 'gov.mt',
		'net.mt', 'com.mu', 'co.mu', 'aero.mv', 'biz.mv', 'com.mv', 'coop.mv', 'edu.mv',
		'info.mv', 'int.mv', 'mil.mv', 'museum.mv', 'name.mv', 'net.mv', 'org.mv',
		'com.mx', 'net.mx', 'org.mx', 'edu.mx', 'gob.mx', 'com.my', 'net.my', 'org.my',
		'edu.my', 'mil.my', 'name.my', 'edu.ng', 'com.ng', 'gov.ng', 'org.ng', 'net.ng',
		'com.ni', 'edu.ni', 'org.ni', 'nom.ni', 'net.ni', 'gov.nr', 'edu.nr', 'biz.nr',
		'com.nr', 'net.nr', 'ac.nz', 'co.nz', 'cri.nz', 'gen.nz', 'geek.nz', 'govt.nz',
		'maori.nz', 'mil.nz', 'net.nz', 'org.nz', 'school.nz', 'com.pf', 'org.pf',
		'com.pg', 'net.pg', 'com.ph', 'gov.ph', 'com.pk', 'net.pk', 'edu.pk', 'org.pk',
		'biz.pk', 'web.pk', 'gov.pk', 'gob.pk', 'gok.pk', 'gon.pk', 'gop.pk', 'gos.pk',
		'biz.pl', 'net.pl', 'art.pl', 'edu.pl', 'org.pl', 'ngo.pl', 'gov.pl', 'info.pl',
		'waw.pl', 'warszawa.pl', 'wroc.pl', 'wroclaw.pl', 'krakow.pl', 'poznan.pl',
		'gda.pl', 'gdansk.pl', 'slupsk.pl', 'szczecin.pl', 'lublin.pl', 'bialystok.pl',
		'torun.pl', 'biz.pr', 'com.pr', 'edu.pr', 'gov.pr', 'info.pr', 'isla.pr',
		'net.pr', 'org.pr', 'pro.pr', 'edu.ps', 'gov.ps', 'sec.ps', 'plo.ps', 'com.ps',
		'net.ps', 'com.pt', 'edu.pt', 'gov.pt', 'int.pt', 'net.pt', 'nome.pt', 'org.pt',
		'net.py', 'org.py', 'gov.py', 'edu.py', 'com.py', 'com.ru', 'net.ru', 'org.ru',
		'msk.ru', 'int.ru', 'ac.ru', 'gov.rw', 'net.rw', 'edu.rw', 'ac.rw', 'com.rw',
		'int.rw', 'mil.rw', 'gouv.rw', 'com.sa', 'edu.sa', 'sch.sa', 'med.sa', 'gov.sa',
		'org.sa', 'pub.sa', 'com.sb', 'gov.sb', 'net.sb', 'edu.sb', 'com.sc', 'gov.sc',
		'org.sc', 'edu.sc', 'com.sd', 'net.sd', 'org.sd', 'edu.sd', 'med.sd', 'tv.sd',
		'info.sd', 'org.se', 'pp.se', 'tm.se', 'parti.se', 'press.se', 'ab.se', 'c.se',
		'e.se', 'f.se', 'g.se', 'h.se', 'i.se', 'k.se', 'm.se', 'n.se', 'o.se', 's.se',
		'u.se', 'w.se', 'x.se', 'y.se', 'z.se', 'ac.se', 'bd.se', 'com.sg', 'net.sg',
		'gov.sg', 'edu.sg', 'per.sg', 'idn.sg', 'edu.sv', 'com.sv', 'gob.sv', 'org.sv',
		'gov.sy', 'com.sy', 'net.sy', 'ac.th', 'co.th', 'in.th', 'go.th', 'mi.th',
		'net.th', 'ac.tj', 'biz.tj', 'com.tj', 'co.tj', 'edu.tj', 'int.tj', 'name.tj',
		'org.tj', 'web.tj', 'gov.tj', 'go.tj', 'mil.tj', 'com.tn', 'intl.tn', 'gov.tn',
		'ind.tn', 'nat.tn', 'tourism.tn', 'info.tn', 'ens.tn', 'fin.tn', 'net.tn',
		'gov.tp', 'com.tr', 'info.tr', 'biz.tr', 'net.tr', 'org.tr', 'web.tr', 'gen.tr',
		'dr.tr', 'bbs.tr', 'name.tr', 'tel.tr', 'gov.tr', 'bel.tr', 'pol.tr', 'mil.tr',
		'edu.tr', 'co.tt', 'com.tt', 'org.tt', 'net.tt', 'biz.tt', 'info.tt', 'pro.tt',
		'edu.tt', 'gov.tt', 'gov.tv', 'edu.tw', 'gov.tw', 'mil.tw', 'com.tw', 'net.tw',
		'idv.tw', 'game.tw', 'ebiz.tw', 'club.tw', 'co.tz', 'ac.tz', 'go.tz', 'or.tz',
		'com.ua', 'gov.ua', 'net.ua', 'edu.ua', 'org.ua', 'cherkassy.ua', 'ck.ua',
		'cn.ua', 'chernovtsy.ua', 'cv.ua', 'crimea.ua', 'dnepropetrovsk.ua', 'dp.ua',
		'dn.ua', 'if.ua', 'kharkov.ua', 'kh.ua', 'kherson.ua', 'ks.ua',
		'km.ua', 'kiev.ua', 'kv.ua', 'kirovograd.ua', 'kr.ua', 'lugansk.ua', 'lg.ua',
		'lviv.ua', 'nikolaev.ua', 'mk.ua', 'odessa.ua', 'od.ua', 'poltava.ua', 'pl.ua',
		'rv.ua', 'sebastopol.ua', 'sumy.ua', 'ternopil.ua', 'te.ua', 'uzhgorod.ua',
		'vn.ua', 'zaporizhzhe.ua', 'zp.ua', 'zhitomir.ua', 'zt.ua', 'co.ug', 'ac.ug',
		'go.ug', 'ne.ug', 'or.ug', 'ac.uk', 'co.uk', 'gov.uk', 'ltd.uk', 'me.uk',
		'mod.uk', 'net.uk', 'nic.uk', 'nhs.uk', 'org.uk', 'plc.uk', 'police.uk', 'bl.uk',
		'jet.uk', 'nel.uk', 'nls.uk', 'parliament.uk', 'sch.uk', 'ak.us', 'al.us',
		'az.us', 'ca.us', 'co.us', 'ct.us', 'dc.us', 'de.us', 'dni.us', 'fed.us',
		'ga.us', 'hi.us', 'ia.us', 'id.us', 'il.us', 'in.us', 'isa.us', 'kids.us',
		'ky.us', 'la.us', 'ma.us', 'md.us', 'me.us', 'mi.us', 'mn.us', 'mo.us', 'ms.us',
		'nc.us', 'nd.us', 'ne.us', 'nh.us', 'nj.us', 'nm.us', 'nsn.us', 'nv.us', 'ny.us',
		'ok.us', 'or.us', 'pa.us', 'ri.us', 'sc.us', 'sd.us', 'tn.us', 'tx.us', 'ut.us',
		'va.us', 'wa.us', 'wi.us', 'wv.us', 'wy.us', 'edu.uy', 'gub.uy', 'org.uy',
		'net.uy', 'mil.uy', 'com.ve', 'net.ve', 'org.ve', 'info.ve', 'co.ve', 'web.ve',
		'org.vi', 'edu.vi', 'gov.vi', 'com.vn', 'net.vn', 'org.vn', 'edu.vn', 'gov.vn',
		'ac.vn', 'biz.vn', 'info.vn', 'name.vn', 'pro.vn', 'health.vn', 'com.ye',
		'ac.yu', 'co.yu', 'org.yu', 'edu.yu', 'ac.za', 'city.za', 'co.za', 'edu.za',
		'law.za', 'mil.za', 'nom.za', 'org.za', 'school.za', 'alt.za', 'net.za',
		'tm.za', 'web.za', 'co.zm', 'org.zm', 'gov.zm', 'sch.zm', 'ac.zm', 'co.zw',
		'gov.zw', 'ac.zw', 'com.ac', 'edu.ac', 'gov.ac', 'net.ac', 'mil.ac', 'org.ac',
		'net.ae', 'co.ae', 'gov.ae', 'ac.ae', 'sch.ae', 'org.ae', 'mil.ae', 'pro.ae',
		'com.ag', 'org.ag', 'net.ag', 'co.ag', 'nom.ag', 'off.ai', 'com.ai', 'net.ai',
		'gov.al', 'edu.al', 'org.al', 'com.al', 'net.al', 'com.am', 'net.am', 'org.am',
		'net.ar', 'org.ar', 'e164.arpa', 'ip6.arpa', 'uri.arpa', 'urn.arpa', 'gv.at',
		'co.at', 'or.at', 'com.au', 'net.au', 'asn.au', 'org.au', 'id.au', 'csiro.au',
		'edu.au', 'com.aw', 'com.az', 'net.az', 'org.az', 'com.bb', 'edu.bb', 'gov.bb',
		'org.bb', 'com.bd', 'edu.bd', 'net.bd', 'gov.bd', 'org.bd', 'mil.be', 'ac.be',
		'com.bm', 'edu.bm', 'org.bm', 'gov.bm', 'net.bm', 'com.bn', 'edu.bn', 'org.bn',
		'com.bo', 'org.bo', 'net.bo', 'gov.bo', 'gob.bo', 'edu.bo', 'tv.bo', 'mil.bo',
		'agr.br', 'am.br', 'art.br', 'edu.br', 'com.br', 'coop.br', 'esp.br', 'far.br',
		'g12.br', 'gov.br', 'imb.br', 'ind.br', 'inf.br', 'mil.br', 'net.br', 'org.br',
		'rec.br', 'srv.br', 'tmp.br', 'tur.br', 'tv.br', 'etc.br', 'adm.br', 'adv.br',
		'ato.br', 'bio.br', 'bmd.br', 'cim.br', 'cng.br', 'cnt.br', 'ecn.br', 'eng.br',
		'fnd.br', 'fot.br', 'fst.br', 'ggf.br', 'jor.br', 'lel.br', 'mat.br', 'med.br',
		'not.br', 'ntr.br', 'odo.br', 'ppg.br', 'pro.br', 'psc.br', 'qsl.br', 'slg.br',
		'vet.br', 'zlg.br', 'dpn.br', 'nom.br', 'com.bs', 'net.bs', 'org.bs', 'com.bt',
		'gov.bt', 'net.bt', 'org.bt', 'co.bw', 'org.bw', 'gov.by', 'mil.by', 'ac.cr',
		'ed.cr', 'fi.cr', 'go.cr', 'or.cr', 'sa.cr', 'com.cy', 'biz.cy', 'info.cy',
		'pro.cy', 'net.cy', 'org.cy', 'name.cy', 'tm.cy', 'ac.cy', 'ekloges.cy',
		'parliament.cy', 'com.dm', 'net.dm', 'org.dm', 'edu.dm', 'gov.dm', 'biz.fj',
		'info.fj', 'name.fj', 'net.fj', 'org.fj', 'pro.fj', 'ac.fj', 'gov.fj', 'mil.fj',
		'com.gh', 'edu.gh', 'gov.gh', 'org.gh', 'mil.gh', 'co.hu', 'info.hu', 'org.hu',
		'sport.hu', 'tm.hu', '2000.hu', 'agrar.hu', 'bolt.hu', 'casino.hu', 'city.hu',
		'erotika.hu', 'film.hu', 'forum.hu', 'games.hu', 'hotel.hu', 'ingatlan.hu',
		'konyvelo.hu', 'lakas.hu', 'media.hu', 'news.hu', 'reklam.hu', 'sex.hu',
		'suli.hu', 'szex.hu', 'tozsde.hu', 'utazas.hu', 'video.hu', 'ac.id', 'co.id',
		'go.id', 'ac.il', 'co.il', 'org.il', 'net.il', 'k12.il', 'gov.il', 'muni.il',
		'co.im', 'net.im', 'gov.im', 'org.im', 'nic.im', 'ac.im', 'org.jm', 'ac.jp',
		'co.jp', 'ed.jp', 'go.jp', 'gr.jp', 'lg.jp', 'ne.jp', 'or.jp', 'hokkaido.jp',
		'iwate.jp', 'miyagi.jp', 'akita.jp', 'yamagata.jp', 'fukushima.jp', 'ibaraki.jp',
		'gunma.jp', 'saitama.jp', 'chiba.jp', 'tokyo.jp', 'kanagawa.jp', 'niigata.jp',
		'ishikawa.jp', 'fukui.jp', 'yamanashi.jp', 'nagano.jp', 'gifu.jp', 'shizuoka.jp',
		'mie.jp', 'shiga.jp', 'kyoto.jp', 'osaka.jp', 'hyogo.jp', 'nara.jp',
		'tottori.jp', 'shimane.jp', 'okayama.jp', 'hiroshima.jp', 'yamaguchi.jp',
		'kagawa.jp', 'ehime.jp', 'kochi.jp', 'fukuoka.jp', 'saga.jp', 'nagasaki.jp',
		'oita.jp', 'miyazaki.jp', 'kagoshima.jp', 'okinawa.jp', 'sapporo.jp',
		'yokohama.jp', 'kawasaki.jp', 'nagoya.jp', 'kobe.jp', 'kitakyushu.jp', 'per.kh',
		'edu.kh', 'gov.kh', 'mil.kh', 'net.kh', 'org.kh', 'net.lb', 'org.lb', 'gov.lb',
		'com.lb', 'com.lc', 'org.lc', 'edu.lc', 'gov.lc', 'army.mil', 'navy.mil',
		'music.mobi', 'ac.mw', 'co.mw', 'com.mw', 'coop.mw', 'edu.mw', 'gov.mw',
		'museum.mw', 'net.mw', 'org.mw', 'mil.no', 'stat.no', 'kommune.no', 'herad.no',
		'vgs.no', 'fhs.no', 'museum.no', 'fylkesbibl.no', 'folkebibl.no', 'idrett.no',
		'org.np', 'edu.np', 'net.np', 'gov.np', 'mil.np', 'org.nr', 'com.om', 'co.om',
		'ac.com', 'sch.om', 'gov.om', 'net.om', 'org.om', 'mil.om', 'museum.om',
		'pro.om', 'med.om', 'com.pa', 'ac.pa', 'sld.pa', 'gob.pa', 'edu.pa', 'org.pa',
		'abo.pa', 'ing.pa', 'med.pa', 'nom.pa', 'com.pe', 'org.pe', 'net.pe', 'edu.pe',
		'gob.pe', 'nom.pe', 'law.pro', 'med.pro', 'cpa.pro', 'vatican.va',
		'com.ar', 'edu.ar', 'gob.ar', 'gov.ar', 'int.ar', 'mil.ar', 'tur.ar',
	);

	/**
	 * Detect domain name in string
	 *
	 * @static
	 * @param  string $string
	 * @return bool|string
	 */
	public static function detectDomain($string)
	{
		// clean garbage from the beginning of the string
		#$string = preg_replace('|^\W+|us', '', $string);
		$string = trim($string);
		$string = strtolower($string);

		// try standart solution
		$domain = parse_url($string, PHP_URL_HOST);

		// otherwise use simplified regex (match scheme+host parts only)
		if (!$domain) {
			$pattern = '!';

			// start with http/https only
			$pattern .= "^(?:https?://)?";

			// host-opening dot (optional)
			$pattern .= '\.?';

			// simplified host pattern (see HOST_PATTERN_RELAXED)
			$pattern .= self::HOST_PATTERN_RELAXED_FLEXIBLE;

			// after host we only expect: port | slash | backslash | query string | end-of-line
			$pattern .= '(:|/|\\\|\?|$)';

			// enough!
			$pattern .= "!u";

			if (preg_match ($pattern, $string, $result)) {
				$domain = $result['host'];
			}
		}

		// final cleanup: remove 'www', check if tld defined
		if ($domain) {
			$domain = ltrim($domain, '.');

			//  check roots here
			if (!self::getTld($domain))
				return false;

			// validate
			if (!preg_match(self::HOST_PATTERN_HARDENED_RE, $domain))
				return false;

			// remove 'www' subdomain
			while ('www' == self::getSubdomain($domain) && self::getLvl($domain) > 1) {
				$domain = preg_replace('|^(www\.)|', '', $domain);
			}

			// check for IDNA domains
			foreach (explode('.', $domain) as $part) {
				if (substr($part, 0, 4) == 'xn--')
					return false;
			}

			return $domain;
		}
	}

	/**
	 * Get dns from get_dns_record or dig
	 *
	 * @param   string	 $domain
	 * @return  array
	 */
	public static function getDNS($domain)
	{
		$dns = self::getRecordDns($domain);

		if (empty($dns)) {
			$dns = self::getDigDns($domain);
		}

		return $dns;
	}

	/**
	 * Get record from tracing dig
	 *
	 * @param   string	  $domain
	 * @return  array|false
	 */
	private static function getDigDns($domain)
	{
		$domain = self::detectDomain($domain);
		$commandOutput  = `dig +trace $domain -t NS`;

		preg_match_all(
		/*
		 *  Output views like:
		 *	 ;; Received 495 bytes from 128.8.10.90#53(d.root-servers.net) in 143 ms
		 *	 freedownload3.com.	172800	IN	NS	ns1.dnstool.net.
		 *	 freedownload3.com.	172800	IN	NS	ns2.dnstool.net.
		 */
			"#$domain\.[\s]+[\d]+[\s]+IN[\s]+NS[\s]+(.*)\.#ui",
			$commandOutput,
			$result
		);

		if (isset($result[1]) && $result[1]) {
			return array_unique($result[1]);
		}

		return array();
	}

	/**
	 * Try to get DNS for site, if not found, recursively go to upper domain level
	 *
	 * @param  $domainName
	 * @return array
	 */
	private static function getRecordDns($domainName)
	{
		$dns = array();
		$dns_raw = dns_get_record($domainName, DNS_NS);

		// www.state.ny.us return records, state.ny.us - not
		if (empty($dns_raw)) {
			$dns_raw = dns_get_record('www.' . $domainName, DNS_NS);
		}

		if (empty($dns_raw)) {
			$parts = explode('.', $domainName);
			if (count($parts) > 2) {
				$domainName = implode('.', array_slice($parts, 1));
				return self::getRecordDns($domainName);
			}
		}

		foreach($dns_raw as $server) {
			if (self::detectDomain($server['target'])) {
				$dns[] = $server['target'];
			}
		}

		return $dns;
	}

	/**
	 * Check if domain exists and reachable
	 *
	 * @static
	 * @param $domain
	 * @return bool
	 */
	public static function isDomainReachable($domain)
	{
		$ip = gethostbyname($domain);

		if ($ip !== $domain) {
			return true;
		} else {
			$ip = gethostbyname('www.' . $domain);
			if (ip2long($ip)) {
				return true;
			}
		}

		return false;
	}

	public static function isGoogleDomain($domain)
	{
		$domain = self::detectDomain($domain);

		return
			$domain
			&& preg_match('/^google\.(com|ae|com\.af|com\.ag|off\.ai|am|com\.ar|as|at|com\.au|az|ba|com\.bd|be|bg|com\.bh|bi|com\.bo|com\.br|bs|by|co\.bw|com\.bz|ca|cd|cg|ch|ci|co\.ck|cl|cn|com\.co|co\.cr|com.cu|cz|de|dj|dk|dm|com.do|com\.ec|ee|com\.eg|es|com\.et|fi|com\.fj|fm|fr|ge|gg|com\.gi|gl|gm|gr|com\.gt|gy|com\.hk|hn|hr|ht|hu|co\.id|ie|co\.il|co\.im|co\.in|is|it|co\.je|com\.jm|jo|co\.jp|co\.ke|kg|co\.kr|kz|li|lk|co\.ls|lt|lu|lv|com\.ly|co\.ma|md|mn|ms|com\.mt|mu|mw|com\.mx|com\.my|com\.na|com\.nf|com\.ng|com\.ni|nl|no|com\.np|nr|nu|co\.nz|com\.om|com\.pa|com\.pe|com\.ph|com\.pk|pl|pn|com\.pr|pt|com\.py|com\.qa|ro|ru|rw|com\.sa|com\.sb|sc|se|com\.sg|sh|si|sk|sn|sm|com\.sv|co\.th|com\.tj|tm|to|tp|com\.tr|tt|com\.tw|com\.ua|co\.ug|co\.uk|com\.uy|co\.uz|com\.vc|co\.ve|vg|co\.vi|com\.vn|vu|ws|co\.za|co\.zm)/i', $domain);
	}

	/**
	 * Get 1+2-level domain.
	 *
	 * E.g:
	 *  - google.com -> google.com;
	 *  - translate.google.com -> google.com;
	 *  - smth.smwr.google.com -> google.com
	 *
	 * @param $domain
	 * @return string
	 */
	public static function getRootDomain($domain)
	{
		$parts = explode('.', $domain);
		$count = count($parts);

		if ($count < 2) return null;

		$topDomain = $parts[$count-2] . '.' . $parts[$count-1];
		if (in_array($topDomain, self::$validTlds) && $count >= 3) {
			$topDomain = $parts[$count-3] . '.' . $topDomain;
		}
		return $topDomain;
	}

	/**
	 * Get 2-level domain.
	 *
	 * E.g:
	 *  - google.com -> google;
	 *  - translate.google.com -> google;
	 *  - smth.smwr.google.com -> google
	 *
	 * @param  $domain
	 * @return
	 */
	public static function getRootDomainWithoutTld($domain)
	{
		$domainWithouttld = self::getDomainWithoutTld($domain);

		if ($domainWithouttld) {
			$labels = explode('.', $domainWithouttld);
			return end($labels);
		}
	}

	/**
	 * Get the domain lvl (WI-compatible)
	 *
	 * google.com lvl=1
	 * translate.google.com lvl=2
	 *
	 * @static
	 * @param $domain
	 * @return int
	 */
	public static function getLvl($domain)
	{
		$domainWithoutTld = self::getDomainWithoutTld($domain);

		if ($domainWithoutTld)
			return sizeof(explode('.', $domainWithoutTld));
	}

	/**
	 * Get full domain name without TLD
	 *
	 *
	 * E.g:
	 *  - google.com -> google;
	 *  - translate.google.com -> translate.google;
	 *  - smth.smwr.google.com -> smth.smwr.google
	 *
	 * @static
	 * @param $domain
	 * @return string
	 */
	public static function getDomainWithoutTld($domain)
	{
		$tld = self::getTld($domain);

		if ($tld)
			return substr(
				$domain,
				0,
				-(1 + strlen($tld)) // +1 because of the leading dot
			);
	}

	/**
	 * Get the max-level domain
	 *
	 *
	 * E.g:
	 *  - google.com -> google;
	 *  - translate.google.com -> translate;
	 *  - smth.smwr.google.com -> smth
	 *
	 * @static
	 * @param $domain
	 * @return string
	 */
	public static function getSubdomain($domain)
	{
		$domainWithouttld = self::getDomainWithoutTld($domain);

		if ($domainWithouttld) {
			$labels = explode('.', $domainWithouttld);
			return reset($labels);
		}
	}

	/**
	 * Gets the tld
	 *
	 * E.g:
	 *  - google.com -> com;
	 *  - translate.google.com -> com;
	 *  - smth.smwr.google.com -> com
	 *  - google.co.uk -> co.uk
	 *
	 * @static
	 * @param $domain
	 * @return string
	 */
	public static function getTld($domain)
	{
		$chunks = explode('.', $domain);

		if (sizeof($chunks) < 2) return null;

		if (sizeof($chunks) > 1) { // check for TLDs
			$tld = join('.', array_slice($chunks, -2));
			if (in_array($tld, self::$validTlds)) {
				return
					sizeof($chunks) > 2
						? $tld:
						null;
			}
		}

		$root = end($chunks);
		if (in_array($root, self::$roots)) {
			return $root;
		}
	}

	/**
	 * Checks the string against IP patterns (both string and arithmetic)
	 *
	 * @param $string
	 * @return bool
	 */
	public static function isIp($string)
	{
		$pattern = '#^' . self::IP_PATTERN . '$#';
		$isIp = preg_match($pattern, $string);

		if ($isIp) {
			$isIpValid = (long2ip(ip2long($string)) == $string);

			if ($isIpValid) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Detect IP in string
	 *
	 * @param $string
	 * @return string|bool
	 */
	public static function detectIp($string)
	{
		$pattern = '#^(?:(?P<scheme>\w+)://)?(?P<IP>' . self::IP_PATTERN . ')#';
		if (preg_match($pattern, $string, $matches)) {
			return $matches['IP'];
		}

		return false;
	}
}