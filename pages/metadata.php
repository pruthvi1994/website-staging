<h1> Freenet Metadata Spec

</h1>
<br/>Author: Adam Langley, Eric Norige
<br/><hr/>
<br/><h2>Table of Contents
</h2>
<br/><ul><li> <a href="#Na3f">Overview</a>
</li><li> <a href="#Na8c">Part Spec</a>
</li><ul><li> <a href="#Na91">Redirect (CDC)</a>
</li><li> <a href="#Na9f">DateRedirect (CDC)</a>

</li><li> <a href="#Nae0">SplitFile (CDC)</a></dt>
</li><li> <a href="#Nb55">Info (MC)</a></dt>
</li><li> <a href="#Nb63">ExtInfo (MC)</a>
</li></ul><li> <a href="#Nb72">Examples</a>
</li><li> <a href="#Nb9a">Handling Other Commands</a>
</li></ul>
<h2><a name="Na3b">Abstract</a>

</h2>This spec is for client metadata.  The purpose of this spec is to provide convenient functionality for building web sites inside freenet.
<br/>
<br/><h2><a name="Na3f">Overview</a>
</h2>
<br/>Metadata = Version command, followed by set of Parts
<br/>
<br/>Part = name of Part, followed by a number of fields, followed by EndPart (or End for the last Part)
<br/>
<br/>Version command = A Part with the name "Version" and a field called "Revision" of type number. This is 1 for the current spec.  Following that an optional "Encoding" field allows for all non-version parts to be gzipped.
<br/>
<br/>More formally
<br/>
<br/><font face="courier" size="2">&lt;Metadata&gt;&nbsp;=&nbsp;&lt;Version&nbsp;command&gt;&nbsp;&lt;Parts&gt;*&nbsp;&lt;LastPart&gt;&nbsp;&lt;Arbitrary&nbsp;Metadata&gt;

</font><br/><font face="courier" size="2">&lt;Version&nbsp;command&gt;&nbsp;=&nbsp;"Version\n"&nbsp;"Revision=1\n"&nbsp;&lt;&nbsp;Optional:&nbsp;"Encoding=gzip\n"&gt;&nbsp;"EndPart\n"
</font><br/><font face="courier" size="2">&lt;Part&gt;&nbsp;=&nbsp;"Document\n"&nbsp;&lt;Field&gt;*&nbsp;"EndPart\n"
</font><br/><font face="courier" size="2">&lt;LastPart&gt;&nbsp;=&nbsp;"Document\n"&nbsp;&lt;Field&gt;*&nbsp;"End\n"

</font><br/><font face="courier" size="2">&lt;Field&gt;&nbsp;=&nbsp;&lt;Key&gt;&nbsp;"="&nbsp;&lt;Value&gt;&nbsp;"\n"&nbsp;
</font><br/>
<br/><h3><a name="Na69">Example 1. Abstract Metadata Format</a>
</h3>
<br/>
<br/><font face="courier" size="2">Version
</font><br/><font face="courier" size="2">Revision=1
</font><br/><font face="courier" size="2">EndPart<a&nbsp;href='tiki-editpage.php?page=EndPart'&nbsp;class='wiki'>?</a>&nbsp;

</font><br/><font face="courier" size="2">Document&nbsp;
</font><br/><font face="courier" size="2">Key=value
</font><br/><font face="courier" size="2">EndPart
</font><br/><font face="courier" size="2">Document
</font><br/><font face="courier" size="2">Key1=value1
</font><br/><font face="courier" size="2">Key2=value2
</font><br/><font face="courier" size="2">End
</font><br/>
<br/><h3><a name="Na74">Note</a>
</h3>
<br/>"//" in a URI is reserved for metadata processing. This means that the MSK@.....// format is gone.  Document Names are the string which comes after the "//".  Each Part should have a field with key "Name".  The Part whose Name matches the Document Name should be acted upon.
<br/>
<br/>Control documents are allowed to have a 'default' Part, serving the function of index.html on the web.  This Part is distinguished by not having a Name field.  This part must be matched with a URI having no text after the "//" seperator.  The default Part should *not* be returned when an unknown name is asked for.
<br/>
<br/>The order of keys in a Part is not important, and if the same Key appears twice in a part, only the last one's value is used.

<br/>
<br/><h3><a name="Na7d">Note</a>
</h3>
<br/>All numbers are base 16
<br/>
<br/>Control Document Commands are denoted (CDC) and metadata commands as (MC)
<br/>
<br/>There may only be one section with the same Name.
<br/>
<br/><h2><a name="Na8c">Part Spec</a>
</h2>
<br/><h3><a name="Na91">Redirect (CDC)</a>
</h3>
<br/>

<br/><font face="courier" size="2">Redirect.Target=&lt;URI:&gt;
</font><br/>
<br/>The Client should redirect to the given URI.
<br/>
<br/><h3><a name="Na9f">DateRedirect (CDC)</a>
</h3>
<br/>
<br/><font face="courier" size="2">&nbsp;&nbsp;[DateRedirect.Increment=&lt;number:&nbsp;time-grain&nbsp;size&nbsp;in&nbsp;seconds,&nbsp;default=15180&nbsp;(one&nbsp;day)&gt;]

</font><br/><font face="courier" size="2">&nbsp;&nbsp;[DateRedirect.Offset=&lt;number:&nbsp;time-grain&nbsp;in&nbsp;seconds&nbsp;since&nbsp;unix&nbsp;epoch&nbsp;(January&nbsp;1,&nbsp;1970)&nbsp;to&nbsp;start&nbsp;increments,&nbsp;default&nbsp;=&nbsp;0&gt;]

</font><br/><font face="courier" size="2">&nbsp;&nbsp;DateRedirect.Target=&lt;URI:&gt;
</font><br/>
<br/>
<br/><ul><li> The date format is in UNIX format - seconds from the epoch
</li></ul>
<ul><li> Only applies to KSKs and SSKs</a>
</li></ul>
<ul><li> The date must be taken UTC (TZ offset = 0)
</li></ul>
The client should take the current time (GMT) and work out the last
<br/>member of the series of times (offset, offset + i, offset + 2i, offset + 3i,

<br/>...) which occurred. The client then replaces the part of the URI after the final
<br/>slash (/string) with &lt;DATE&gt;-string where &lt;DATE&gt; is the hex encoded number of seconds
<br/>since the epoch.
<br/>
<br/><h3><a name="Nac1">Note</a>
</h3>
<br/>"freenet:" is speced otherwise someone could do something like "http://..."
<br/>
<br/>In the case of KSKs, the human readable part is the whole key, so <tt>freenet:KSK@style</tt> becomes <tt>freenet:KSK@3b4cf86e-style</tt>

<br/>
<br/>In the case of SSKs, the human readable part is the document name, and <tt>freenet:SSK@aabbccddee/style</tt> becomes <tt>freenet:SSK@aabbccddee/3b4cf86e-style</tt>
<br/>
<br/>
<br/><h3> <a name="Nae0">SplitFile (CDC)</a>
</h3>
<br/><div class='simplebox' align='center'>note 12 Feb 2003: Splitfiles without FEC are outdated. <a href='tiki-index.php?page=Userpagemihi' class='wiki'>mihi</a></div>
<br/>

<br/>
<br/><font face="courier" size="2">&nbsp;&nbsp;SplitFile.Size=&lt;hex&nbsp;file&nbsp;size&gt;
</font><br/><font face="courier" size="2">&nbsp;&nbsp;SplitFile.BlockCount=&lt;hex&nbsp;no.&nbsp;of&nbsp;data&nbsp;blocks&gt;
</font><br/><font face="courier" size="2">&nbsp;&nbsp;<a&nbsp;class='wiki'&nbsp;&nbsp;href='SplitFile.CheckBlockCount=&nbsp;&lt;hex.&nbsp;no&nbsp;of&nbsp;check&nbsp;blocks&gt;'>SplitFile.CheckBlockCount=&nbsp;&lt;hex.&nbsp;no&nbsp;of&nbsp;check&nbsp;blocks&gt;</a>

</font><br/><font face="courier" size="2">&nbsp;&nbsp;SplitFile.Block.&lt;n&gt;=&lt;URI&gt;
</font><br/><font face="courier" size="2">&nbsp;&nbsp;<a&nbsp;class='wiki'&nbsp;&nbsp;href='SplitFile.Graph.&lt;x&gt;=&nbsp;a,b,c...'>SplitFile.Graph.&lt;x&gt;=&nbsp;a,b,c...</a>
</font><br/>
<br/>
<br/><ul><li> <a name="Naec"></a>0 &lt;= n &lt;= 65535
</li><li> <a name="Naf2"></a>n must be sequential, starting with 1, in the metadata part

</li><li><a name="Naf8"></a>n is represented in base 16
</li></ul>
The document is made up of a number of pieces, allowing swarming.
<br/>
<br/><h3><a name="Nb02">Note</a>
</h3>
<br/>thanks much to thelema, oierw, mjr and others for this
<br/>
<br/>
<br/><dl><dt>Size <i>Required</i></dt><dd> This defines the final size of the original file.
</dd></dl><br/><dl><dt>BlockCount&nbsp;<i>Required</i></dt><dd> This defines the number of pieces of data that there are.

</dd></dl><br/><dl><dt>CheckBlockCount&nbsp;<i>Optional</i></dt><dd> This defines the number of check pieces that there are.  If Omitted, should default to 0.
</dd></dl><br/><dl><dt>Block.&lt;n&gt; <i>Required</i></dt><dd> These are the block URIs, most likely CHKs. These must be numbered 1 to BlockCount+CheckBlockCount.  The first BlockCount blocks are the data blocks, and the next CheckBlockCount are the check blocks. From the above, a client can start a swarmed <b style="color:black;background-color:#ffff66">download</b> of the file. Redundant splitting is optional, and information is below.
</dd></dl><br/><dl><dt>Graph.&lt;x&gt; <i>Optional</i></dt><dd> For each check Block.&lt;x&gt;, there must be a Graph.&lt;x&gt; listing the data blocks that check block derives from.  Graph entries for 1..BlockCount should not be given, but for BlockCount+1..BlockCount+CheckBlockCount must be given.  A check block may also be derived from other check blocks, but only lower numbered ones.

</dd></dl><br/>
<br/><h3><a name="Nb55">Info (MC) </a>
</h3>
<br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;<a&nbsp;class='wiki'&nbsp;&nbsp;href='Info.Format=&lt;string:&nbsp;MIME-type&gt;'>Info.Format=&lt;string:&nbsp;MIME-type&gt;</a>
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;<a&nbsp;class='wiki'&nbsp;&nbsp;href='Info.Description=&lt;string:&nbsp;freeform&gt;'>Info.Description=&lt;string:&nbsp;freeform&gt;</a>
</font><br/>
<br/>The Info.* namespace is reserved for Dublin Core metadata.  Prepend "Info." to the keys you want to use to prevent collisions.  See http://www.freenetproject.org/doc/infometadata.html for details.
<br/>
<br/>Format is the proper place to put the document's mime type. Description is a Plain description of this data, not an abstract or TOC.
<br/>

<br/>
<br/><h3><a name="Nb63">ExtInfo (MC)</a>
</h3>
<br/>
<br/><font face="courier" size="2">&nbsp;&nbsp;<a&nbsp;class='wiki'&nbsp;&nbsp;href='ExtInfo.Trailing=yes'>ExtInfo.Trailing=yes</a>
</font><br/><font face="courier" size="2">&nbsp;&nbsp;<a&nbsp;class='wiki'&nbsp;&nbsp;href='ExtInfo.URI=&lt;URI&gt;'>ExtInfo.URI=&lt;URI&gt;</a>
</font><br/>
<br/>
<br/>If ExtInfo.Trailing is set to yes, the metadata for this file will include all data after the final "End" in the control document. If the ExtInfo.URI parameter exists, the contents of the URI pointed to should be included in the metadata for the current document.
<br/><h2><a name="Nb72">Examples</a>
</h2>
<br/><b>Example 2. Pseudo Website</b>

<br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Version
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Revision=1
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;EndPart<a&nbsp;href='tiki-editpage.php?page=EndPart'&nbsp;class='wiki'>?</a>
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Document
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Redirect.Target=CHK@aabbccddee
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;EndPart<a&nbsp;href='tiki-editpage.php?page=EndPart'&nbsp;class='wiki'>?</a>
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Document
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Name=split
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;SplitFile.Size=102400
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;SplitFile.BlockCount=3
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;SplitFile.Block.1=freenet:CHK@aabbccddee1
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;SplitFile.Block.2=freenet:CHK@aabbccddee2
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;SplitFile.Block.3=freenet:CHK@aabbccddee3

</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Info.Format=text/plain
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;EndPart<a&nbsp;href='tiki-editpage.php?page=EndPart'&nbsp;class='wiki'>?</a>
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Document
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Name=date-redirect
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;DateRedirect.Increment=93a80
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;DateRedirect.Offset=a8c0
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;DateRedirect.Target=SSK@aabbccddee/something
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;End
</font><br/>
<br/>This would declare a sort of website. Assume it is inserted under
<br/>freenet:SSK@aabbccddee/mysite. Accessing freenet:SSK@aabbccddee/mysite
<br/>or freenet:SSK@aabbccddee/mysite// would cause the first redirect
<br/>(without a Name) to be processed.
<br/>
<br/>If freenet:SSK@aabbccddee/mysite//split were accessed the SplitFile<a href='tiki-editpage.php?page=SplitFile' class='wiki'>?</a>

<br/>section would be processed, as would the Info section. This would
<br/>(hopefully) swarm a file, with some configurable concurrency. None of the CHKs 
<br/>being swarmed need any metadata because it's included in the control document. 
<br/>
<br/>If freenet:SSK@aabbccddee/mysite//date-redirect were accessed the
<br/>DateRedirect section would be processed. This would redirect to some other
<br/>URI.
<br/>
<br/><a name="Nb8b"></a><b>Example 3.  TrailingInfo example </b>
<br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Version
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Revision=1
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;EndPart<a&nbsp;href='tiki-editpage.php?page=EndPart'&nbsp;class='wiki'>?</a>
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Document
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Redirect.Target=CHK@aabbccddee

</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;ExtInfo.Trailing=yes
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;EndPart<a&nbsp;href='tiki-editpage.php?page=EndPart'&nbsp;class='wiki'>?</a>
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Document
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Name=doc1
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Redirect.Target=CHK@aabbccddee1
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Info.Format=text/plain
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;ExtInfo.Trailing=yes
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;EndPart<a&nbsp;href='tiki-editpage.php?page=EndPart'&nbsp;class='wiki'>?</a>
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Document
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;Name=doc2
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;DateRedirect.Target=SSK@aabbccddee/something
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;ExtInfo.URI=CHK@eeddccbbaa
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;End

</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;&lt;XML&nbsp;blah&nbsp;blah&gt;
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;blah&nbsp;blah
</font><br/><font face="courier" size="2">&nbsp;&nbsp;&nbsp;&lt;/XML&gt;
</font><br/>
<br/>This describes the same website as above, but with metadata in a trailing field.  It's completely reasonable for multiple documents to share the same TrailingInfo<a href='tiki-editpage.php?page=TrailingInfo' class='wiki'>?</a> metadata.  (Since only one needs to be processed, this shouldn't be a problem) 
<br/>
<br/><h2><a name="Nb9a">Handling Other Commands</a>
</h2>

<br/><div class='simplebox' align='center'>This section is Deprecated pending further revision of the spec</div>
<br/>
<br/>Other commands may be inserted if they follow the general structure of
<br/>metadata commands set out above.
<br/>
<br/>These commands may set an Importance header of type string. "Required"
<br/>means that the client should ALWAYS stop processing if it doesn't
<br/>understand this extended command. "Informational" means that the
<br/>client should NEVER stop processing if it doesn't understand this
<br/>extended command. "Optional" means it's up to the client to
<br/>decide.
<br/>
<br/>If not given, Importance defaults to Informational.
<br/>
<br/>Since a client will process, for a given Name, the first
<br/>command it understands, you can do the following:

<br/>
<br/><h3><a name="Nbae">Note</a>
</h3>
<br/>WaitRedirect is an example of an extended command, not a speced command
<br/>
<br/><a name="Nbb4"><b>Example 4. An Extended Command</b></a>
<br/>
<br/>
<br/><font face="courier" size="2">&nbsp;&nbsp;WaitRedirect.Time=5
</font><br/><font face="courier" size="2">&nbsp;&nbsp;WaitRedirect.Target=CHK@gargargargar
</font><br/><font face="courier" size="2">&nbsp;&nbsp;Redirect.Target=freenet:CHK@gargargargar
</font><br/>
<br/>The WaitRedirect will override the Redirect, if the client understands
<br/>WaitRedirects.
<br/>

<br/>-- SebastianSpaeth - 24 Feb 2002<br/>