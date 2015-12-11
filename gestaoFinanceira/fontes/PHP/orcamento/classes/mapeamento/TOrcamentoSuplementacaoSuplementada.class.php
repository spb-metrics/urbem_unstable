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
    * Classe de mapeamento da tabela ORCAMENTO.SUPLEMENTACAO_SUPLEMENTADA
    * Data de Criação: 10/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.24
                    uc-02.01.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrcamentoSuplementacaoSuplementada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('orcamento.suplementacao_suplementada');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_suplementacao,exercicio,cod_despesa');

    $this->AddCampo('exercicio'        ,'char'    ,true,'04'   ,true, true);
    $this->AddCampo('cod_suplementacao','integer' ,true,''     ,true,false);
    $this->AddCampo('cod_despesa'      ,'integer' ,true,''     ,true,false);
    $this->AddCampo('valor'            ,'numeric' ,true,'14,2' ,true,false);

}

}

?>