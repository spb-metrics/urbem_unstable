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
--/**
--    * Função PLSQL
--    * Data de Criação: 09/09/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23402 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-20 16:57:16 -0300 (Qua, 20 Jun 2007) $
--
--    * Casos de uso: uc-04.05.11
--*/

CREATE OR REPLACE FUNCTION  calculaFolhaDecimo(INTEGER,CHAR,BOOLEAN,VARCHAR,VARCHAR) RETURNS BOOLEAN as $$ 
DECLARE
    inCodContratoParametro          ALIAS FOR $1;
    stDesdobramentoParametro        ALIAS FOR $2;
    boErro                          ALIAS FOR $3;
    stEntidadeParametro          ALIAS FOR $4;
    stExercicioParametro            ALIAS FOR $5;
    stEntidade                   VARCHAR := '';
    stExercicioSistema              VARCHAR := '';
    boRetorno                       BOOLEAN := TRUE;
    inCodContrato                   INTEGER; 
    inCodContratoGeradorBeneficio   INTEGER; 
    inPensionista                   INTEGER;
    inCodEspecialidade              INTEGER;
    inCodFuncao                     INTEGER;
    inCodPeriodoMovimentacao        INTEGER;
    inCodRegime                     INTEGER;
    inCodSubDivisao                 INTEGER;
    inCodConfiguracao               INTEGER;
    inCodPrevidenciaOficial         INTEGER;
    inCodServidor                   INTEGER;
    inNumCGM                        INTEGER;
    inControle                      INTEGER := 1;
    stDataFinalCompetencia          VARCHAR := '';
    stTipoFolha                     VARCHAR := 'D';
    stDesdobramento                 VARCHAR := '';    
BEGIN
    boRetorno := removerTodosBuffers();
    stEntidade               := criarBufferTexto('stEntidade',stEntidadeParametro);
    stExercicioSistema          := criarBufferTexto('stExercicioSistema',stExercicioParametro);
    stTipoFolha                 := criarBufferTexto('stTipoFolha',stTipoFolha);
    inCodPeriodoMovimentacao    := pega0CodigoPeriodoMovimentacaoAberta(  ); 
    inCodPeriodoMovimentacao    := criarBufferInteiro(  'inCodPeriodoMovimentacao' , inCodPeriodoMovimentacao  ); 
    stDataFinalCompetencia      := Pega0DataFinalCompetenciaDoPeriodoMovimento(  inCodPeriodoMovimentacao  ); 
    stDataFinalCompetencia      := criarBufferTexto(  'stDataFinalCompetencia' , stDataFinalCompetencia  ); 
    inCodContratoGeradorBeneficio := pega0ContratoDoGeradorBeneficio(inCodContratoParametro);
    
    IF inCodContratoGeradorBeneficio IS NULL THEN
        inCodContrato := inCodContratoParametro;
        inPensionista := criarBufferInteiro('inPensionista',0);
    ELSE
        inCodContrato := inCodContratoGeradorBeneficio;
        inPensionista := criarBufferInteiro('inPensionista',1);
    END IF;
    inCodContrato               := CriarBufferInteiro('inCodContrato',inCodContrato);
    
    inCodRegime                 := Pega0RegimeDoContratoNaData(  inCodContrato , stDataFinalCompetencia  ); 
    inCodRegime                 := criarBufferInteiro(  'inCodRegime' , inCodRegime  ); 
    inCodSubDivisao             := Pega0SubDivisaoDoContratoNaData(  inCodContrato , stDataFinalCompetencia  ); 
    inCodSubDivisao             := criarBufferInteiro(  'inCodSubDivisao' , inCodSubDivisao  ); 
    inCodFuncao                 := Pega0FuncaoDoContratoNaData(  inCodContrato , stDataFinalCompetencia  ); 
    inCodFuncao                 := criarBufferInteiro(  'inCodFuncao' , inCodFuncao  ); 
    inCodEspecialidade          := Pega0EspecialidadeDoContratoNaData(  inCodContrato , stDataFinalCompetencia  ); 
    inCodEspecialidade          := criarBufferInteiro(  'inCodEspecialidade' , inCodEspecialidade  ); 
    inCodConfiguracao           := criarBufferInteiro(  'inCodConfiguracao', 3 );
    inCodServidor               := Pega0ServidorDoContrato(inCodContrato);
    inCodServidor               := criarBufferInteiro('inCodServidor',inCodServidor);
    inNumCGM                    := Pega0NumCGMServidor(inCodServidor);
    inNumCGM                    := criarBufferInteiro('inNumCgm',InNumCGM);    
    stDesdobramento             := criarBufferTexto('stDesdobramento',stDesdobramentoParametro);
    IF inCodContratoGeradorBeneficio IS NOT NULL THEN
        inCodContrato := criarBufferInteiro('inCodContrato',inCodContratoParametro);        
    END IF;
    
    inCodPrevidenciaOficial     := Pega1PrevidenciaOficialDoContrato();
    inCodPrevidenciaOficial     := criarBufferInteiro('inCodPrevidenciaOficial',inCodPrevidenciaOficial);

    --Varável utilizada no controle para a função pegaValorCalculadoFixo
    --Esta variável controla se o valor será gravado em banco ou apenas em memória
    inControle                  := criarBufferInteiro(  'inControle', inControle  );

    boRetorno := processarEventosAutomaticosDecimo();
    boRetorno := criarTemporariaRegistrosFixos();
    IF   boErro  =  FALSE THEN
        boRetorno := calculaEventoDecimoPorContrato(); 
    ELSE
        boRetorno := calculaEventoDecimoPorContratoErro(); 
    END IF;

    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql'; 
