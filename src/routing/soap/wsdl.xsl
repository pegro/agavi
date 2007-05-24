<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:agavi="http://agavi.org/agavi/1.0/config"
xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/"
xmlns="http://schemas.xmlsoap.org/wsdl/"
>
	<xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" />
	<xsl:variable name="tns" select="name(/agavi:configurations/namespace::*[.=../@targetNamespace])" />
	<xsl:variable name="targetNamespace" select="/agavi:configurations/@targetNamespace" />
	<xsl:variable name="request_postfix" select="'Request'" />
	<xsl:variable name="response_postfix" select="'Response'" />
	<xsl:template match="/agavi:configurations">
		<wsdl:definitions name="Dummy">
			<xsl:copy-of select="namespace::*"/>
			<!-- copy targetNamespace -->
			<xsl:copy-of select="@targetNamespace" />
			<!-- copy type defs -->
			<xsl:apply-templates select="wsdl:types | wsdl:message" />
			<!-- all the rest -->
			<xsl:apply-templates select="agavi:configuration[.//agavi:route//wsdl:* | .//agavi:route//soap:*]" />
		</wsdl:definitions>
	</xsl:template>
	<xsl:template match="agavi:configuration">
		<wsdl:portType name="DummyPortType">
			<xsl:apply-templates select=".//agavi:route" mode="port" />
		</wsdl:portType>
		<binding name="DummyBinding" type="tns:DummyPortType">
			<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
			<xsl:apply-templates select=".//agavi:route" mode="binding" />
		</binding>
		<service name="DummyService">
			<port name="DummyPort" binding="tns:DummyBinding">
				<soap:address location="DummySoapLocation" />
			</port>
		</service>
		<xsl:apply-templates select=".//agavi:route" mode="messages" />
	</xsl:template>
	<xsl:template match="agavi:route" mode="port">
		<xsl:variable name="name" select="translate(@pattern, '^$', '')" />
		<wsdl:operation name="{$name}">
			<xsl:apply-templates select="wsdl:input | wsdl:output" mode="portType_operation">
				<xsl:with-param name="name" select="$name" />
			</xsl:apply-templates>
		</wsdl:operation>
	</xsl:template>
	<xsl:template match="wsdl:input[wsdl:part or wsdl:message/wsdl:part or soap:body[not(@message)]/wsdl:part or soap:body[not(@message)]/wsdl:message/wsdl:part]" mode="portType_operation">
		<xsl:param name="name" />
		<wsdl:input message="{$tns}:{$name}Request" />
	</xsl:template>
	<xsl:template match="wsdl:input[@message] | wsdl:input/soap:body[@message]" mode="portType_operation">
		<xsl:param name="name" />
		<wsdl:input>
			<xsl:attribute name="message"><xsl:value-of select="@message" /></xsl:attribute>
		</wsdl:input>
	</xsl:template>
	<xsl:template match="wsdl:output[wsdl:part or wsdl:message/wsdl:part or soap:body[not(@message)]/wsdl:part or soap:body[not(@message)]/wsdl:message/wsdl:part]" mode="portType_operation">
		<xsl:param name="name" />
		<wsdl:output message="{$tns}:{$name}Response" />
	</xsl:template>
	<xsl:template match="wsdl:output[@message] | wsdl:output/soap:body[@message]" mode="portType_operation">
		<xsl:param name="name" />
		<wsdl:output>
			<xsl:attribute name="message"><xsl:value-of select="@message" /></xsl:attribute>
		</wsdl:output>
	</xsl:template>
	<xsl:template match="agavi:route" mode="messages">
		<xsl:variable name="name" select="translate(@pattern, '^$', '')" />
		<xsl:apply-templates select="wsdl:input | wsdl:output" mode="message">
			<xsl:with-param name="name" select="$name" />
		</xsl:apply-templates>
	</xsl:template>
	<xsl:template match="wsdl:input[wsdl:part or wsdl:message/wsdl:part or soap:body[not(@message)]/wsdl:part or soap:body[not(@message)]/wsdl:message/wsdl:part or soap:header[not(@message)]/wsdl:part or soap:header[not(@message)]/wsdl:message/wsdl:part]" mode="message">
		<xsl:param name="name" />
		<xsl:apply-templates select="wsdl:part | wsdl:message/wsdl:part | soap:body[not(@message)]/wsdl:part | soap:body[not(@message)]/wsdl:message/wsdl:part" mode="message">
			<xsl:with-param name="name" select="$name" />
			<xsl:with-param name="postfix" select="$request_postfix" />
		</xsl:apply-templates>
		<xsl:if test="soap:header[not(@message)]/wsdl:part | soap:header[not(@message)]/wsdl:message/wsdl:part">
			<wsdl:message name="{$name}{$request_postfix}Headers">
				<xsl:copy-of select="soap:header[not(@message)]/wsdl:part | soap:header[not(@message)]/wsdl:message/wsdl:part" />
			</wsdl:message>
		</xsl:if>
	</xsl:template>
	<xsl:template match="wsdl:output[wsdl:part or wsdl:message/wsdl:part or soap:body[not(@message)]/wsdl:part or soap:body[not(@message)]/wsdl:message/wsdl:part or soap:header[not(@message)]/wsdl:part or soap:header[not(@message)]/wsdl:message/wsdl:part]" mode="message">
		<xsl:param name="name" />
		<xsl:apply-templates select="wsdl:part | wsdl:message/wsdl:part | soap:body[not(@message)]/wsdl:part | soap:body[not(@message)]/wsdl:message/wsdl:part" mode="message">
			<xsl:with-param name="name" select="$name" />
			<xsl:with-param name="postfix" select="$response_postfix" />
		</xsl:apply-templates>
		<xsl:if test="soap:header[not(@message)]/wsdl:part | soap:header[not(@message)]/wsdl:message/wsdl:part">
			<wsdl:message name="{$name}{$response_postfix}Header">
				<xsl:copy-of select="soap:header[not(@message)]/wsdl:part | soap:header[not(@message)]/wsdl:message/wsdl:part" />
			</wsdl:message>
		</xsl:if>
	</xsl:template>
	<xsl:template match="wsdl:part | wsdl:message/wsdl:part | soap:body[not(@message)]/wsdl:part | soap:body[not(@message)]/wsdl:message/wsdl:part" mode="message">
		<xsl:param name="name" />
		<xsl:param name="postfix" />
		<wsdl:message name="{$name}{$postfix}">
			<xsl:copy-of select="." />
		</wsdl:message>
	</xsl:template>
	<xsl:template match="agavi:route" mode="binding">
		<xsl:variable name="name" select="translate(@pattern, '^$', '')" />
		<wsdl:operation name="{$name}">
			<soap:operation soapAction="{$targetNamespace}#{$name}" />
			<xsl:apply-templates select="wsdl:input" mode="binding_operation">
				<xsl:with-param name="name" select="$name" />
				<xsl:with-param name="postfix" select="$request_postfix" />
			</xsl:apply-templates>
			<xsl:apply-templates select="wsdl:output" mode="binding_operation">
				<xsl:with-param name="name" select="$name" />
				<xsl:with-param name="postfix" select="$response_postfix" />
			</xsl:apply-templates>
		</wsdl:operation>
	</xsl:template>
	<xsl:template match="wsdl:input | wsdl:output" mode="binding_operation">
		<xsl:param name="name" />
		<xsl:param name="postfix" />
		<xsl:copy>
			<soap:body namespace="{$targetNamespace}">
				<xsl:if test="soap:body">
					<xsl:copy-of select="soap:body/@encodingStyle | soap:body/@namespace | soap:body/@use" />
				</xsl:if>
			</soap:body>
			<xsl:if test="soap:header">
				<xsl:for-each select="soap:header">
					<soap:header namespace="{$targetNamespace}">
						<xsl:copy-of select="@encodingStyle | @message | @namespace | @part | @use" />
						<xsl:if test=".//wsdl:part">
							<xsl:attribute name="message"><xsl:value-of select="$tns" />:<xsl:value-of select="$name" /><xsl:value-of select="$postfix" />Headers</xsl:attribute>
							<xsl:attribute name="part"><xsl:value-of select=".//wsdl:part/@name" /></xsl:attribute>
						</xsl:if>
					</soap:header>
				</xsl:for-each>
			</xsl:if>
		</xsl:copy>
	</xsl:template>
	<xsl:template match="/wsdl:types | /wsdl:message">
		<xsl:copy>
			<xsl:copy-of select="node() | @*" />
		</xsl:copy>
	</xsl:template>
</xsl:stylesheet>