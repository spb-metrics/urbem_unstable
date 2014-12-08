/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_consulta_endereco_mata_saojoao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.11
*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_endereco_mata_saojoao( INTEGER )  RETURNS varchar AS '
DECLARE
    inImovel    ALIAS FOR $1;
    stRetorno   VARCHAR;
    
BEGIN
    --imcc => endereco correspondencia do imovel
    --imv => endereco do imovel
    --ip => endereco do cgm
    SELECT
        CASE WHEN (ip.cod_municipio = 251 AND ip.cod_uf = 5) OR ( ip.cod_municipio = 0 AND ip.cod_uf = 0 ) THEN
            CASE WHEN imcc.cod_municipio = 251 AND imcc.cod_uf = 5 THEN
                imcc.endereco
            ELSE
                imv.endereco
            END
        ELSE
            ip.endereco
        END AS endereco

    INTO
        stRetorno

    FROM
        imobiliario.imovel AS ii
     
    LEFT JOIN (
        SELECT
            IMC.inscricao_municipal,
            TL.nom_tipo||''§''||
            IMC.cod_logradouro||''§''||
            LOGRN.nom_logradouro||''§''||
            IMC.numero||''§''||
            IMC.complemento||''§''||
            BAIRRO.nom_bairro||''§''||
            IMC.cep||''§''||
            MUN.cod_municipio||''§''||
            MUN.nom_municipio||''§''||
            UF.cod_uf||''§''||
            UF.sigla_uf
            AS endereco,
            MUN.cod_municipio,
            UF.cod_uf,
            IMC.timestamp
    
        FROM
            imobiliario.imovel_correspondencia  as IMC
    
        INNER JOIN 
            sw_uf as UF
        ON 
            UF.cod_uf = IMC.cod_UF
    
        INNER JOIN 
            sw_municipio as MUN
        ON 
            MUN.cod_uf = UF.cod_UF
            AND MUN.cod_municipio = IMC.cod_municipio
    
        INNER JOIN 
            sw_bairro as BAIRRO
        ON 
            BAIRRO.cod_uf = UF.cod_UF
            AND BAIRRO.cod_municipio = MUN.cod_municipio
            AND BAIRRO.cod_bairro = IMC.cod_bairro
    
        INNER JOIN 
            sw_logradouro as LOGR
        ON 
            LOGR.cod_logradouro = IMC.cod_logradouro
            AND LOGR.cod_municipio = IMC.cod_municipio
            AND LOGR.cod_uf = IMC.cod_uf
    
        INNER JOIN 
            sw_nome_logradouro as LOGRN
        ON 
            LOGRN.cod_logradouro = LOGR.cod_logradouro
    
        INNER JOIN 
            sw_tipo_logradouro as TL
        ON 
            TL.cod_tipo = LOGRN.cod_tipo
    
    ) as imcc
    ON
        imcc.inscricao_municipal = ii.inscricao_municipal
    
    LEFT JOIN (
        SELECT
            i.inscricao_municipal,
            TL.nom_tipo||''§''||
            l.cod_logradouro||''§''||
            nl.nom_logradouro||''§''||
            i.numero||''§''||
            i.complemento||''§''||
            bairro.nom_bairro||''§''||
            i.cep||''§''||
            mun.cod_municipio||''§''||
            mun.nom_municipio||''§''||
            uf.cod_uf||''§''||
            uf.sigla_uf
            AS endereco,
            --TL.nom_tipo||''§''||l.cod_logradouro||''§''||nl.nom_logradouro||''§''||i.numero||''§''||i.complemento||''§''||i.cep||''§''||''''||''§''||bairro.nom_bairro||''§''||mun.cod_municipio||''§''||mun.nom_municipio||''§''||uf.cod_uf||''§''||uf.sigla_uf||''§''||i.inscricao_municipal AS endereco,
            uf.cod_uf,
            mun.cod_municipio
    
        FROM
            imobiliario.imovel AS i
    
        INNER JOIN
            imobiliario.imovel_confrontacao ic
        ON
            ic.inscricao_municipal = i.inscricao_municipal
    
        INNER JOIN 
            imobiliario.confrontacao_trecho ct
        ON 
            ct.cod_confrontacao  = ic.cod_confrontacao AND
            ct.cod_lote             = ic.cod_lote
    
        INNER JOIN 
            imobiliario.trecho t
        ON 
            t.cod_trecho     = ct.cod_trecho     AND
            t.cod_logradouro    = ct.cod_logradouro
    
        INNER JOIN 
            sw_logradouro l
        ON 
            l.cod_logradouro = t.cod_logradouro
    
        INNER JOIN 
            sw_nome_logradouro nl
        ON 
            nl.cod_logradouro = l.cod_logradouro
                                            
        INNER JOIN 
            sw_tipo_logradouro tl
        ON 
            tl.cod_tipo       = nl.cod_tipo
    
        INNER JOIN 
            imobiliario.lote_bairro as ilb
        ON 
            ilb.cod_lote = ic.cod_lote
            AND ilb.cod_municipio = l.cod_municipio
            AND ilb.cod_uf = l.cod_uf
    
        INNER JOIN 
            sw_bairro as bairro
        ON 
            bairro.cod_bairro = ilb.cod_bairro
            AND bairro.cod_municipio = l.cod_municipio
            AND bairro.cod_uf = l.cod_uf
    
        INNER JOIN 
            sw_municipio as mun
        ON 
            mun.cod_municipio = l.cod_municipio
            AND mun.cod_uf = l.cod_uf
    
        INNER JOIN 
            sw_uf as uf
        ON 
            uf.cod_uf = mun.cod_uf
    )AS imv
    ON
        imv.inscricao_municipal = ii.inscricao_municipal
    
    LEFT JOIN (
        SELECT
            ip.inscricao_municipal,
            cgm.tipo_logradouro_corresp||''§''||
            ''0''||''§''||
            cgm.logradouro_corresp||''§''||
            cgm.numero_corresp||''§''||
            cgm.complemento_corresp||''§''||
            cgm.bairro_corresp||''§''||
            cgm.cep_corresp||''§''||
            MUN.cod_municipio||''§''||
            MUN.nom_municipio||''§''||
            UF.cod_uf||''§''||
            UF.sigla_uf
            AS endereco,
            UF.cod_uf,
            MUN.cod_municipio,
            ip.timestamp
    
        FROM
            imobiliario.proprietario  as ip
    
        INNER JOIN
            sw_cgm AS cgm
        ON
            cgm.numcgm = ip.numcgm
    
        INNER JOIN 
            sw_uf as UF
        ON 
            UF.cod_uf = cgm.cod_uf_corresp
    
        INNER JOIN 
            sw_municipio as MUN
        ON 
            MUN.cod_uf = UF.cod_uf
            AND MUN.cod_municipio = cgm.cod_municipio_corresp
    
    ) as ip
    ON
        ip.inscricao_municipal = ii.inscricao_municipal
    
    WHERE                                                                  
        ii.inscricao_municipal = inImovel
    
    ORDER BY
        imcc.timestamp desc
    LIMIT 1;

    RETURN stRetorno;
END;
' LANGUAGE 'plpgsql';
