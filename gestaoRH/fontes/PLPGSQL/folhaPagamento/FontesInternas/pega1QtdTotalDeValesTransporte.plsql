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

-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/04/24 10:50:00 $
--
-- Caso de uso: uc-04.05.45
-- Caso de uso: uc-04.05.48
--
-- Objetivo: apurar a quantidade total de vales transporte, seja como lancamento
-- individualizado ou por grupos, independente de seu tipo e valor.
-- Como ainda nao ha relacao entre period de leitura e periodo de desconto do vale 
-- sera tratado ate la como data final da competencia limitando o uso do vale ao mes 
-- de competencia tanto para leitura dos dias como para o lançamento do desconto. 
--/



CREATE OR REPLACE FUNCTION pega1QtdTotalDeValesTransporte() RETURNS integer as '

DECLARE
    inCodContrato              INTEGER;
    stDataFinalCompetencia     VARCHAR := '''';

    inMes                      INTEGER := 1;
    inAno                      INTEGER := 2006;

    dtTimestamp               date ;

    inQtdTotalVales           INTEGER := 0;

    inQtdValesAvulsos         INTEGER := 0;

    inQtdValesGrupo           INTEGER := 0;

    stSql                     VARCHAR;
    stSql2                    VARCHAR;
    reRegistro                RECORD;
    reRegistro2               RECORD;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    stDataFinalCompetencia := recuperarBufferTexto(''stDataFinalCompetencia'');
    
    inCodContrato := recuperarBufferInteiro(''inCodContrato'');
    inCodContrato := recuperaContratoServidorPensionista(inCodContrato);
    dtTimestamp := to_date(substr( stDataFinalCompetencia ,1,10),''yyyy-mm-dd'');

    inMes := substr(stDataFinalCompetencia,6,2)::INTEGER;
    inAno := substr(stDataFinalCompetencia,1,4)::INTEGER;
   
/*
    -- leitura registro especifico de vale transporte
*/
    stSql := '' SELECT 
       beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.cod_contrato
       ,beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.cod_mes
       ,beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.cod_concessao
       ,beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.exercicio  
       ,beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.vigencia

       ,beneficio''||stEntidade||''.concessao_vale_transporte.cod_vale_transporte as cod_vale_transporte
       ,beneficio''||stEntidade||''.concessao_vale_transporte.cod_tipo
       ,beneficio''||stEntidade||''.concessao_vale_transporte.quantidade as quantidade

       FROM beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte 

       LEFT OUTER JOIN beneficio''||stEntidade||''.concessao_vale_transporte 
         ON beneficio''||stEntidade||''.concessao_vale_transporte.cod_concessao 
            = beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.cod_concessao
        AND beneficio''||stEntidade||''.concessao_vale_transporte.cod_mes = ''||inMes||''
        AND beneficio''||stEntidade||''.concessao_vale_transporte.exercicio = ''||inAno||''

       LEFT OUTER JOIN beneficio''||stEntidade||''.vale_transporte
         ON beneficio''||stEntidade||''.vale_transporte.cod_vale_transporte 
            = beneficio''||stEntidade||''.concessao_vale_transporte.cod_vale_transporte

      WHERE beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.cod_contrato = ''||inCodContrato||''
        AND beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.cod_mes = ''||inMes||''
        AND beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.exercicio = ''||inAno||''
        AND beneficio''||stEntidade||''.contrato_servidor_concessao_vale_transporte.vigencia <= ''''''||dtTimestamp||'''''' 
      '';

    FOR reRegistro IN  EXECUTE stSql LOOP

              inQtdValesAvulsos := inQtdValesAvulsos + reRegistro.quantidade;

    END LOOP;

/*
    -- leitura de grupo
*/

    stSql2 := '' SELECT 
      beneficio''||stEntidade||''.contrato_servidor_grupo_concessao_vale_transporte.cod_contrato
     ,beneficio''||stEntidade||''.contrato_servidor_grupo_concessao_vale_transporte.cod_grupo

     ,beneficio''||stEntidade||''.grupo_concessao_vale_transporte.cod_concessao

     ,beneficio''||stEntidade||''.concessao_vale_transporte.cod_vale_transporte
     ,beneficio''||stEntidade||''.concessao_vale_transporte.cod_tipo
     ,beneficio''||stEntidade||''.concessao_vale_transporte.quantidade

     FROM beneficio''||stEntidade||''.contrato_servidor_grupo_concessao_vale_transporte

      LEFT OUTER JOIN beneficio''||stEntidade||''.grupo_concessao_vale_transporte 
        ON beneficio''||stEntidade||''.grupo_concessao_vale_transporte.cod_grupo 
           = beneficio''||stEntidade||''.contrato_servidor_grupo_concessao_vale_transporte.cod_grupo 
       AND beneficio''||stEntidade||''.grupo_concessao_vale_transporte.cod_mes = ''||inMes||''
       AND beneficio''||stEntidade||''.grupo_concessao_vale_transporte.exercicio = ''||inAno||''
       AND beneficio''||stEntidade||''.grupo_concessao_vale_transporte.vigencia <= ''''''||dtTimestamp||''''''


      LEFT OUTER JOIN beneficio''||stEntidade||''.concessao_vale_transporte 
        ON beneficio''||stEntidade||''.concessao_vale_transporte.cod_concessao 
           = beneficio''||stEntidade||''.grupo_concessao_vale_transporte.cod_concessao 
       AND beneficio''||stEntidade||''.concessao_vale_transporte.cod_mes = ''||inMes||''
       AND beneficio''||stEntidade||''.concessao_vale_transporte.exercicio = ''||inAno||''

      LEFT OUTER JOIN beneficio''||stEntidade||''.vale_transporte
        ON beneficio''||stEntidade||''.vale_transporte.cod_vale_transporte 
           = beneficio''||stEntidade||''.concessao_vale_transporte.cod_vale_transporte

     WHERE beneficio''||stEntidade||''.contrato_servidor_grupo_concessao_vale_transporte.cod_contrato = ''||inCodContrato||''
       AND beneficio''||stEntidade||''.grupo_concessao_vale_transporte.cod_grupo is not null

     '' ;

    FOR reRegistro2 IN  EXECUTE stSql2 LOOP

        inQtdValesGrupo := inQtdValesGrupo + reRegistro2.quantidade;

    END LOOP;

    inQtdTotalVales := inQtdValesAvulsos + inQtdValesGrupo;

    RETURN inQtdTotalVales;

END;

' LANGUAGE 'plpgsql';



