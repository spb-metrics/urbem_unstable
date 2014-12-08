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
    * Arquivo de mapeamento para a função que busca os dados do arquivo receitaPrev
    * Data de Criação   : 22/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGReceitaPrev extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTCEMGReceitaPrev()
{
    parent::Persistente();

    $this->setTabela('tcemg.fn_receita_prev');

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "
        SELECT mes
             , contrib_pat
             , contrib_serv_ativo
             , contrib_serv_inat_pens
             , rec_patrimoniais
             , alienacao_bens
             , outras_rec_cap
             , comp_prev
             , outras_rec
             , cod_tipo
             , contrib_pat_anterior
             , repasses_prev
             , receitas_prev_intra
          FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                     , '".$this->getDado("cod_entidade")."'
                                     , ".$this->getDado("mes")."
                                     ) AS retorno(
                                                  mes                       INTEGER
                                                , contrib_pat               NUMERIC(14,2)
                                                , contrib_serv_ativo        NUMERIC(14,2)
                                                , contrib_serv_inat_pens    NUMERIC(14,2)
                                                , rec_patrimoniais          NUMERIC(14,2)
                                                , alienacao_bens            NUMERIC(14,2)
                                                , outras_rec_cap            NUMERIC(14,2)
                                                , comp_prev                 NUMERIC(14,2)
                                                , outras_rec                NUMERIC(14,2)
                                                , cod_tipo                  VARCHAR
                                                , contrib_pat_anterior      NUMERIC(14,2)
                                                , repasses_prev             NUMERIC(14,2)
                                                , receitas_prev_intra       NUMERIC(14,2)
                                                 )";

    return $stSql;
}

}
