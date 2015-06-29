<?php
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
?>
<?php
/**
    * Arquivo de mapeamento para a função que busca os dados  de despesa do pessoal
    * Data de Criação   : 22/01/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes

    * @package URBEM
    * @subpackage

    $Id: FTCEMGDespesaTotalPessoalPE.class.php 62814 2015-06-23 14:42:37Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDespesaTotalPessoalPE extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGDespesaTotalPessoalPE()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_despesa_total_pessoal_pe');

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);    
    $this->AddCampo('mes'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('data_inicial'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('data_final'    ,'varchar',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "
        SELECT ".$this->getDado('mes')." as mes
             , ABS(vencVantagens) as vencVantagens         
             , ABS(inativos) as  inativos            
             , ABS(pensionistas) as    pensionistas     
             , ABS(salarioFamilia) as      salarioFamilia  
             , ABS(subsPrefeito) as      subsPrefeito    
             , ABS(subsVice) as   subsVice           
             , ABS(subsSecret) as  subsSecret          
             , ABS(obrigPatronais) as  obrigPatronais      
             , ABS(repassePatronal) as  repassePatronal     
             , ABS(sentJudPessoal) as  sentJudPessoal      
             , ABS(indenDemissao) as      indenDemissao   
             , ABS(incDemVolunt) as incDemVolunt         
             , ABS(sentJudAnt) as   sentJudAnt         
             , ABS(inatPensFontCustProp) as inatPensFontCustProp 
             , ABS(outrasDespesasPessoal) as outrasDespesasPessoal
             , nadaDeclararPessoal
             , ABS(despExercAnt) as   despExercAnt       
             , ABS(exclusaoDespAnteriores) as exclusaoDespAnteriores
             , ABS(corrPerApurac) as      corrPerApurac   
             , ABS(despCorres) as       despCorres     
             , ABS(despAnteriores) as    despAnteriores    
          FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                     , '".$this->getDado("cod_entidade")."'                                     
                                     , '".SistemaLegado::dataToSql($this->getDado("data_inicial"))."'
                                     , '".SistemaLegado::dataToSql($this->getDado("data_final"))."'
                                     ) AS retorno (
                                                  vencVantagens          NUMERIC,
                                                  inativos               NUMERIC,
                                                  pensionistas           NUMERIC,
                                                  salarioFamilia         NUMERIC,
                                                  subsPrefeito           NUMERIC,
                                                  subsVice               NUMERIC,
                                                  subsSecret             NUMERIC,
                                                  obrigPatronais         NUMERIC,
                                                  repassePatronal        NUMERIC,
                                                  sentJudPessoal         NUMERIC,
                                                  indenDemissao          NUMERIC,
                                                  incDemVolunt           NUMERIC,
                                                  sentJudAnt             NUMERIC,
                                                  inatPensFontCustProp   NUMERIC,
                                                  outrasDespesasPessoal  NUMERIC,
                                                  despExercAnt           NUMERIC,
                                                  exclusaoDespAnteriores NUMERIC,
                                                  corrPerApurac          NUMERIC,
                                                  despCorres             NUMERIC,
                                                  despAnteriores         NUMERIC,
                                                  nadaDeclararPessoal    VARCHAR
                                                 )
        ";
    return $stSql;
}

}//END CLASS

