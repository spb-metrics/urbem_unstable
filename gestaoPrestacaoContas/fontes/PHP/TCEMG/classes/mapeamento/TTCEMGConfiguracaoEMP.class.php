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
    * Classe de mapeamento da tabela TCEMG.CONFIGURACAO_LEIS_PPA
    * Data de Criação: 15/01/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: TTCEMGConfiguracaoEMP.class.php 61709 2015-02-26 19:05:09Z carlos.silva $
    *
    * $Name: $
    * $Date: $
    * $Author: $
    * $Rev: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGConfiguracaoEMP extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGConfiguracaoEMP()
    {
        parent::Persistente();
        $this->setTabela('tcemg.arquivo_emp');

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('cod_entidade, cod_empenho');

        $this->AddCampo('exercicio'                 , 'varchar', true,   4,  true, false);
        $this->AddCampo('cod_entidade'              , 'integer', false, '', false,  true);
        $this->AddCampo('cod_empenho'               , 'integer', false, '', false, false);
        $this->AddCampo('cod_licitacao'             , 'integer', false, '', false, false);
        $this->AddCampo('exercicio_licitacao'       , 'varchar', false,  4, false, false);
        $this->AddCampo('cod_modalidade'            , 'integer', false, '', false, false);

    }

}
